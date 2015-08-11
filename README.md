# DataExporterBundle

## About

Data Exporter Bundle for Symfony2.
 
## Basic Usage 

Assume a variable `$items` storing 2 user profile objects:

| id            | firstName     | lastName      | age   |
| ------------- | ------------- | ------------- | ----- |
| 1             | Foo           | Chan          | 20    |
| 2             | Bar           | Wong          | 17    |

Here `$items` is a variable containing a data set or collection. This exporter bundle allows you to have the data set converted to a given format, e.g. CSV or Excel.

To use it, first enable the bundle in `AppKernel.php`.
 
```php
$bundles = array(
    // ....
    new Sparkson\DataExporterBundle\SparksonDataExporterBundle(),
);
```

Now assume you are in a controller action. Here is how the exporter is used:  

```php
public function exportAction(Request $request)
{
    // First, retrieve the ExporterFactory service. 
    $exporterFactory = $this->get('sparkson.data_exporter.factory');
    
    // Then create a new exporter builder instance.
    $builder = $exporterFactory->createBuilder();
    
    // We'll use the builder to build an exporter.
    // Let's define the column structure.
    $builder
        ->add('id', 'string')
        ->add('firstName', 'string')
        ->add('lastName', 'string')
        ->add('age', 'string');
    
    // Build the exporter.
    $exporter = $builder->getExporter();
    
    // Assigns an output adapter. Here we use the CSV adapter that utilizes PHP's fputcsv() function.
    $exporter->setOutput(new \Sparkson\DataExporterBundle\Exporter\Output\CSVAdapter());
    
    // Finally, sets the variable containing the data set to export ($items is assumed here).
    $exporter->setDataSet($items);
    
    // Run!
    $exporter->execute();
    
    // By default, the output adapter will write the result to a variable which can be retrieved via getResult().
    $result = $exporter->getResult();
    
    // Feed the result to the response object  
    return new Response($result);
}
```

The `$result` variable will look like as follow:
```
Id,FirstName,LastName,Age
1,Foo,Chan,20
2,Bar,Wong,17
```

This illustrates the basic usage of the exporter. Before going on, I want to add that like `add()`, you can chain the methods like below:

```php
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

## Defining the column structure

Here is how a column is added:

`$builder->add('id', 'string')`

 * The first parameter is the field name, which has to be unique among the same column set. By default, the library uses Symfony's PropertyAccess component to retrieve the value to export from the data object or array using this field name.
 * The second parameter is the field type. It can be a type name registered in the type registry (normally via the service tag), or a concrete instance implementing `Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface`. For example, you can write `$builder->add('id', new \Sparkson\DataExporterBundle\Exporter\Core\Type\StringType())` and the result will be the same.
 * The third parameter (not shown in the above example) is the options. The available options differ from field types. 

That said, a few option attributes are common among all types.

### label

Each column has a label, which will become the caption in the header row. If unspecified, the label will be generated from the column name. You can override this default behaviour by specifying a value to the `label` attribute.   
    
### property_path

The `property_path` attribute overrides the default behaviour of using the column name as the property path to retrieve value of the column. For example, you may want to write this when the data is an array: 

```php
$builder->add('id', 'string', ['property_path' => '[id]'])
```

Field type does not retrieve a column value directly. Instead it passes a value retrieval request to the value resolver component which can be changed via `Exporter::setValueResolver()`. If unspecified, `Sparkson\DataExporterBundle\Exporter\ValueResolver\DefaultValueResolver` will be used as the default, which uses Symfony's PropertyAccess component to retrieve the column value. Thus property path can be any value understood by the PropertyAccess component when the default value resolver is used. This means that property of an inner object is also supported:   
 
```php
$builder->add('author_name', 'string', ['property_path' => 'author.name'])
```

### resolver_options

Similar to field types, additional options can be supplied to value resolvers. This can be done through the `resolver_options` array in an field type.

For example, `DefaultValueResolver` supports a `filter` option which accepts an array of either:
 * a string which is a simple PHP function; or
 * an instance implementing `Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter\FilterInterface`. One example is the `CustomFilter` class in this library.

Filters serve as value preprocessors, mainly for sanitizing values using functions like `trim()`, `ltrim()` etc. AFTER the value is retried and BEFORE passing it to the field type for final output.

```php
$builder->add('description', 'string', ['resolver_options' => ['filters' => ['trim']]]);
// OR
$builder->add('description', 'string', ['resolver_options' => ['filters' => [ new CustomFilter(function($value) {
    return trim($value);
})]]]);
```

### Field types

Here are the field types provided in this bundle. Classes are defined under the `Sparkson\DataExporterBundle\Exporter\Core\Type` namespace. 

| Class name        | Type          |
| ----------------- | ------------- |
| StringType        | string        |
| BooleanType       | boolean       |
| CallbackType      | callback      |
| DateTimeType      | datetime      |
| MapType           | map           |
| NumberType        | number        |
| RawType           | raw           |

Here shows the use of the `MapType`:
```php
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

For details on how a specific type works and their available options, please consult the source files.

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
```php
$profileExporter = $factory->createExporter(new \AppBundle\Exporter\Type\ProfileType());
// OR, when the class is registered in the type registry:
$profileExporter = $factory->createExporter('user_profile');
```

## Output adapters

When `execute()` is called, the exporter iterates the data set one by one, each with the defined column set. However, extraction of column values from a record row is the job of the field type. Similarly, the exporter does not handle the writing of the extracted values itself. Instead, it delegates the job to an **output adapter**.

The following output adapters are supplied in this library:
 * `CSVAdapter`, which uses PHP's own `fputcsv()` function to write data.
 * `PHPExcelAdapter`, which utilizes the [PHPExcel](https://github.com/PHPOffice/PHPExcel) library to write data.
 * `TwigTemplateOutputAdapter`, which renders the result using a customizable twig template.

Unlike field types, output adapters are not services, so you need to create them manually. Refer to their source codes for details on constructor arguments and available options. Here shows some brief usage examples:

By default, `CSVAdapter` writes data to memory during `$exporter->execute()` which can be retrieved with the `getResult()` method. In the following example, `CSVAdapter` is configured to write the export result to a file instead, and will not keep the result (i.e. it won't read the data back) for further retrieval via `getResult()`:

```php
$exporter->setOutput(new CSVAdapter(array(
    'output' => __DIR__.'/output.csv', // sets output file 
    'keep_result' => false,            // do not keep result for getResult()
)));
```

With `TwigTemplateOutputAdapter`, you can pass the exported data to a Twig template for further processing. This class requires the `Twig_Environment` instance as the first constructor argument. Here is a usage example:     

```php
$twig = $this->get('twig'); // retrieve the Twig_Environment instance from the service container
$exporter->setOutput(new TwigTemplateOutputAdapter($twig, [
    'template' => '@AppBundle/exporter/my_exporter_template.html.twig',
]));
```

Note that the default template located at `Resources/view/exporter/template.html.twig` will be used if no template is given. You can use this file to learn writing your own template.

Hint: You can define your own output adapter services. For example, you can define a service for `TwigTemplateOutputAdapter` which uses your own template. After that the above code example can be simplified as follow:
  
```
$exporter->setOutput($this->get('app.exporter.output_adapter.twig'));
```

Here is how the service is defined in `services.yml`:

```php
app.exporter.output_adapter.twig:
    class: Sparkson\DataExporterBundle\Exporter\Output\TwigTemplateOutputAdapter
    arguments:
        - @twig
        - { template: "@AppBundle/exporter/my_exporter_template.html.twig" }
```

## Misc. notes

### Changing column properties

By default, columns are exported in the order when they are added. But you may want to modify the column order after the exporter is built. Here's how:
```php
$columnSet = $exporter->getColumns();
$columnSet->getChild('lastName')->setPosition(2);
```
Note that position is not guaranteed to be unique. It is legitimate to have nonconsecutive position, or two columns with the same position. 

Alternately, you may re-order the columns via `setColumnOrders()`:

```php
$columnSet->setColumnOrders(['age', 'lastName', 'firstName', 'id']);
// Columns re-arranged in the following order: "age", "last name", "first name", "id"
```

You can also disable a column from the exporter:
```php
$columnSet->getChild('id')->setEnabled(false);
```

You can even disable columns not specified in `setColumnOrders` by passing `true` as the second argument:   
```php
// Fields in the column set: id, age, firstName, lastName
$columnSet->setColumnOrders(['lastName', 'firstName'], true);
// Columns re-arranged in the following order: "last name", "first name". 
// "id" and "age" are disabled.
```

This is especially useful when you provide an UI for users to choose which columns to export. Instead of adding ifs around the builder's add field statements, just add all of them and disable the unwanted ones after the exporter is built. 

**Important: Column properties cannot be changed once `$exporter->execute()` is called.**

### Working with large data sets in Doctrine

To export database records with Doctrine, one would normally write this: 

```php
// $em is the Entity Manager
$items = $em->getRepository('AppBundle:EntityToExport')->findAll();
$exporter->setDataSet($items);
```

But this does not work well for large data sets, as the memory consumption can be very high. Fortunately, Doctrine provides a way to [iterate over a large result set](http://doctrine-orm.readthedocs.org/en/latest/reference/batch-processing.html#iterating-large-results-for-data-processing). Here is the code slightly modified from Doctrine's documentation:
 
```php
$q = $em->createQuery('SELECT e FROM AppBundle:EntityToExport e');
$iterableResult = $q->iterate();
foreach ($iterableResult as $row) {
   // do stuff with the data in the row, $row[0] is always the object

   // detach from Doctrine, so that it can be Garbage-Collected immediately
   $em->detach($row[0]);
}
```

We can adopted this technique using of PHP's [Generator](http://php.net/manual/en/language.generators.overview.php), like so:
```php
$getDataSetIterator = function()
{
    $q = $em->createQuery('SELECT e FROM AppBundle:EntityToExport e');
    $iterableResult = $q->iterate();
    foreach ($iterableResult as $row) {
        yield $row[0];
    
        // detach from Doctrine, so that it can be Garbage-Collected immediately
        $em->detach($row[0]);
    }
};

$exporter->setDataSet($getDataSetIterator());
```

That's it. For PHP < 5.5 you will need to roll your own iterator wrapper class.