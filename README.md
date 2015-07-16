# DataExporterBundle

## About

Data Exporter Bundle for Symfony2.
 
Warning: Documentation and the API are WIP and not stable.

## Basic Usage 

Assume a variable `$items` storing 2 user profile objects:

| id            | firstName     | lastName      | age   |
| ------------- | ------------- | ------------- | ----- |
| 1             | Foo           | Chan          | 20    |
| 2             | Bar           | Wong          | 17    |

This exporter bundle allows you to have the above data converted to a given format, e.g. CSV or Excel.

To use the exporter you need to initialize an `Exporter` instance. Normally you would do this via the `ExporterBuilder` class: 

```
// Of course you need to first enable the bundle.
$bundles = array(
    // ....
    new Sparkson\DataExporterBundle\SparksonDataExporterBundle(),
);

// Assume you are in an action. First retrieve the exporter factory service.
$exporterFactory = $this->get('sparkson.data_exporter.factory');

// Then create a new exporter builder instance.
$builder = $exporterFactory->createBuilder();

// Now you can use the builder to build an exporter. Let's define the column structure.
$builder
    ->add('id', 'string')
    ->add('firstName', 'string')
    ->add('lastName', 'string')
    ->add('age', 'string');

// After that we can build the exporter.
$exporter = $builder->getExporter();

// And set the output adapter. Here we use the CSV adapter that utilizes PHP's fputcsv() function.
$exporter->setOutput(new \Sparkson\DataExporterBundle\Exporter\Output\CSVAdapter());

// Of course we need to set the source data to export.
$exporter->setDataSet($items);

// Run!
$exporter->execute();

//  Get the result
$result = $exporter->getResult();
```

The `$result` variable will look like as follow:
```
Id,FirstName,LastName,Age
1,Foo,Chan,20
2,Bar,Wong,17
```

This illustrates the basic usage of the exporter. Before going on, I want to add that like `add()`, you can chain the methods like below:

```
$result = $builder
    ->add('id', 'string')
    ->add('firstName', 'string')
    ->add('lastName', 'string')
    ->add('age', 'string')
    ->getExporter() // this returns the Exporter instance
    ->setOutput(new \Sparkson\DataExporterBundle\Exporter\Output\CSVAdapter());
    ->setDataSet($items)
    ->execute()
    ->getResult();
```

## Types

Here is how a column is added:

`$builder->add('id', 'string')`

 * The first parameter is the field name, which has to be unique among the same column set. By default the library use Symfony's PropertyAccess component to retrieve the export value from the data object/array using this column name.
 * The second parameter is the field type. It can be a type name registered in the type registry (normally via the service tag), or a concrete instance implementing `Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface`. For example, you can write `$builder->add('id', new \Sparkson\DataExporterBundle\Exporter\Core\Type\StringType())` and the result will be the same.
 * The third parameter (not shown in the above example) is the options. The available options differ from field types. 

That said, a few option attributes are common among all types.

### label

Each column has a label, which will become the caption in the header row. It will be generated from the column name by default, but when the `label` attribute is supplied, it will be used instead.   

### property_path

The `property_path` attribute overrides the default behavior of using the column name as the property path to retrieve value of the column. For example, you may want to write this when the data is an array: 

```
$builder->add('id', 'string', ['property_path' => '[id]'])
```

Field type does not retrieve column value directly. Instead it passes a value retrieval request to the value resolver component which can be changed via `Exporter::setValueResolver()`. If unspecified, `Sparkson\DataExporterBundle\Exporter\ValueResolver\DefaultValueResolver` will be used as the default, which uses Symfony's PropertyAccess component to retrieve the column value. Thus property path can be any value understood by the PropertyAccess component when the default value resolver is used. This means that property of an inner object is also supported:   
 
```
 $builder->add('author_name', 'string', ['property_path' => 'author.name'])
```

### resolver_options

Similar to field types, additional options can be supplied to value resolvers. This can be done through the `resolver_options` array in an field type.

For example, `DefaultValueResolver` supports a `filter` option which accepts an array of either:
 * a string which is a simple PHP function; or
 * an instance implementing `Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter\FilterInterface`. One example is the `CustomFilter` class in this library.

This is mainly provided to sanitize a value using functions like `trim()`, `ltrim()` etc. after retrieving the value and before passing to the field type for final output.

```
$builder->add('description', 'string', ['resolver_options' => ['filters' => ['trim']]]);
// OR
$builder->add('description', 'string', ['resolver_options' => ['filters' => [ new CustomFilter(function($value) {
  return trim($value);
});]]]);
```

This bundle comes with the following field types:

 * `StringType`
 * `BooleanType`
 * `CallbackType`
 * `DateTimeType`
 * `MapType`
 * `NumberType`
 * `RawType`

Here I only illustrate the use of the `MapType`. For other types please refer to their source files.
```
$builder->add('user_type', 'map', ['map' => [
  'U' => 'User',
  'A' => 'Administrator',
  'M' => 'Moderator',
]]);
```

To put it in another way, field types are like value transformers:
 * `MapType` expects a string which should be a key of the provided map. The value of the respective key in the map will be returned. Similarly,
 * `BooleanType` expects a boolean and will transform it into strings like "Yes/No", "Enabled/Disabled" depending on the configuration. 
 * `DateTimeType` transforms the original field value into a formatted datetime. 
 * `StringType` casts the value into a string. In addition, it provides an optional `format` configuration which will be passed to PHP's `sprintf()` function when set.

Lastly, you can add your own field type. Just refer to the source code of existing types on how to implement one, and refer to `services.yml` on how to register the types to the type registry so that you can refer to it merely by its name.

## Defining exporter fields in a separate class

In addition to build an exporter instance using the builder, it is also possible to define exporter fields in a separate class.

```php
<?php

namespace AppBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\Type\AbstractType;

class ProfileType extends AbstractType
{
    public function buildExporter(ExporterBuilder $builder)
    {
        $builder
            ->add('firstName', 'string')
            ->add('lastName', 'string')
            ->add('age', 'string');
    }

    public function getName()
    {
        return 'user_profile';
    }
}
```

The exporter can then be created by passing the class instance to the factory: 
```
$profileExporter = $factory->createExporter(new \AppBundle\Exporter\Type\ProfileType());
// OR, when the class is registered in the type registry:
$profileExporter = $factory->createExporter('user_profile');
```

## Output adapters

The exporter is responsible for iterating the data set by the defined column set, but extraction of columns values from a record are the job of field type. Similarly, the exporter does not handle the writing of the extracted values itself. Instead, it delegates the job to an **output adapter**.

This library provides the following output adapters:
 * `CSVAdapter`, which uses PHP's own `fputcsv()` function to write data.
 * `PHPExcelAdapter`, which utilizes the PHPExcel library to write data (you need to add `phpexcel/phpexcel` to your `composer.json`).
 * `TwigTemplateOutputAdapter`, which renders the result using a customizable twig template.

As they are not service classes you need to initialize them manually. Refer to their source codes for constructor arguments and available options. Here is a sample of how to use the `TwigTemplateOutputAdapter`:

```
$twig = $this->get('twig'); // retrieve the Twig_Environment instance from the service container
$exporter->setOutput($twig, [
    'template' => '@AppBundle/exporter/my_exporter_template.html.twig',
]);
```

Note: The default template located at `Resources/view/exporter/template.html.twig` will be used if no template is given. Refer to this file to learn how to write your own template.

## Misc. notes

### Changing the column properties

By default, columns are exported in the order when the fields are added. But you may want to modify the column order after the exporter is built. Here's how:
```
$columnSet = $exporter->getColumns();
$columnSet->getChild('lastName')->setPosition(2);
```
Note that column position is not guaranteed to be unique. It is legitimate to have nonconsecutive position, or two columns with the same position. Alternately you may re-order the columns via `setColumnOrders()`:
```
// Re-arrange the columns in the specified order: age, last name, first name, id
$columnSet->setColumnOrders(['age', 'lastName', 'firstName', 'id']);
```

You can also disable a column from the exporter:
```
$columnSet->getChild('id')->setEnabled(false);
```

You can even disable columns not specified in `setColumnOrders` by passing `true` as the second argument:   
```
// Fields in the column set: id, age, firstName, lastName
$columnSet->setColumnOrders(['lastName', 'firstName'], true);
// id and age will be disabled. lastName and firstName will be exported in specified order.
```

This is useful when you provide an UI for users to choose which columns to export. Instead of adding ifs around the builder's add field statement, just disable the unselected columns after the exporter is built, and before the exporter is run. 

**Important: Column properties cannot be changed once `$exporter->execute()` is called.**
