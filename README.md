# DataExporterBundle

Note: The API is not stable and is subject to change. 

Data Exporter Bundle for Symfony2.
 
Assume the following table, which contains 2 user profile objects:

| id            | firstName     | lastName      | age   |
| ------------- | ------------- | ------------- | ----- |
| 1             | Foo           | Chan          | 20    |
| 2             | Bar           | Wong          | 17    |

The data structure would look like this in code:

```php
$items = [
    ['id' => 1, 'firstName' => 'Foo', 'lastName' => 'Chan', 'age' => 20],
    ['id' => 2, 'firstName' => 'Bar', 'lastName' => 'Wong', 'age' => 17],
];
```

With this bundle you can transform the data set (`$items`) to another format, like CSV:
```
Id,FirstName,LastName,Age
1,Foo,Chan,20
2,Bar,Wong,17
```

## Basic Usage 

First you need to enable the bundle in `AppKernel.php`:
 
```php
$bundles = array(
    // ....
    new Sparkson\DataExporterBundle\SparksonDataExporterBundle(),
);
```

Now assume you are in a controller action. Here is how to use the exporter:  

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
    $exporter->setOutputAdapter(new \Sparkson\DataExporterBundle\Exporter\OutputAdapter\CSVAdapter());
    
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

This illustrates the basic usage of the exporter. Note that methods can be chained as below:

```php
$result = $builder
    ->add('id', 'string')
    ->add('firstName', 'string')
    ->add('lastName', 'string')
    ->add('age', 'string')
    ->getExporter() // this returns the Exporter instance
    ->setOutputAdapter(new \Sparkson\DataExporterBundle\Exporter\OutputAdapter\CSVAdapter());
    ->setDataSet($items)
    ->execute()
    ->getResult();
```

## Defining the column structure

The  exporter builder allows you to define the column set to be exported using `$builder->add()`. For example:

```php
$builder->add('id', 'string', []);
```

Users of the great [Symfony Form](http://symfony.com/doc/current/book/forms.html) component will find this syntax familiar. This is intentional. 

 * The first parameter is the field name, which has to be unique among the same column set.
 * The second parameter is the field type. It can be a type name registered in the type registry (normally via the service tag), or an object implementing `Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface`. This means that instead of passing the type name you could also write `$builder->add('id', new \Sparkson\DataExporterBundle\Exporter\Core\Type\StringType())` and the result will be the same.
 * The third parameter is the options. 

The `options` argument allows you to further configure the behaviour of a type. The allowed options are different among field types, with a few exceptions like `label` and `property_path`.

### label

Each column has a label, which is used as the caption/title in the header row. If unspecified, the label will be generated from the column name.

### property_path

The `property_path` attribute overrides the default behaviour of using field name as the property path to retrieve the column values For example, you may want to write this when the record is an array: 

```php
$builder->add('id', 'string', ['property_path' => '[id]'])
```

A field type does not retrieve a column value by itself. Instead it passes the `property_path` value to the value resolver component and doesn't care how the value is retrieved. By default, `\Sparkson\DataExporterBundle\Exporter\ValueResolver\DefaultValueResolver` is used which fetches record values with the help of Symfony's [PropertyAccess](http://symfony.com/doc/current/components/property_access/index.html) component. This means that you can write the  following:

```php
$builder->add('author_name', 'string', ['property_path' => 'author.name'])
```

Although it seems unlikely that you will want to roll your own value resolver, it is possible to tell the exporter to use your own value resolver by calling  `Exporter::setValueResolver()`. 

### resolver_options

Additional options can be passed to the active value resolver for each column via the `resolver_options` key.

`DefaultValueResolver` supports a `filters` option which accepts an array of either

 * a string which is a simple PHP function that accepts the exported value as the first parameter; or
 * an instance implementing `Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter\FilterInterface`. One example is the `CustomFilter` class in this library.

Filters serve like value pre-processors, mainly for sanitizing values using functions like `trim()`, `ltrim()` etc. *after* the value is retrieved and *before* the value is passed it to the field type for final output.

```php
$builder->add('description', 'string', ['resolver_options' => ['filters' => ['trim']]]);
// is equivalent to 
$builder->add('description', 'string', ['resolver_options' => ['filters' => [ new CustomFilter(function($value) {
    return trim($value);
})]]]);
```

### Field types

Below are the field types provided in this bundle. Classes are defined under the `Sparkson\DataExporterBundle\Exporter\Core\Type` namespace. 

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

To put it another way, field types are like value transformers:

 * `MapType` expects a string which should be a key of the provided map. During export, `MapType` transforms the column value to the mapped value. Similarly,
 * `BooleanType` expects a boolean and will transform it into strings like "Yes/No", "Enabled/Disabled" depending on the configuration. 
 * `DateTimeType` transforms the original field value into a formatted datetime. 
 * `StringType` casts the value into a string. In addition, it provides an optional `format` configuration which will be passed to PHP's `sprintf()` function when set.

Check the source files for details on how a specific type works and their available options.

Lastly, you can add your own field type to the type registry. Just refer to the source code of existing types on how to implement one, and refer to `services.yml` on how to register the types to the type registry so that you can use it by its name.

## Defining exporter fields in a separate class

Just like Symfony's Form component, you can also define exporter fields in a separate class:

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

When `execute()` is called, the exporter instance iterates the data set one by one, each with the defined column set. But the exporter instance does not handle the writing of the extracted values itself. Such job is delegated to an **output adapter**.

You can find the following output adapters in this library:
 * `CSVAdapter`, which uses PHP's own `fputcsv()` function to write data.
 * `GoogleSpreadsheetAdapter`, which uses [asimlqt/php-google-spreadsheet-client](https://github.com/asimlqt/php-google-spreadsheet-client) to write data to Google Spreadsheet.
 * `PHPExcelAdapter`, which utilizes the [PHPExcel](https://github.com/PHPOffice/PHPExcel) library to write data.
 * `TwigAdapter`, which renders the result using a customizable twig template.

Unlike field types, output adapters are not services, so you need to create them manually. Refer to their source codes for details on constructor arguments and available options. Here shows some brief usage examples:

By default, `CSVAdapter` writes data to memory during `$exporter->execute()` which can be retrieved with the `getResult()` method. In the following example, `CSVAdapter` is configured to write the export result to a file instead, and will not keep the result (i.e. it won't read the data back) for further retrieval via `getResult()`:

```php
$exporter->setOutputAdapter(new CSVAdapter([
    'filename' => __DIR__.'/output.csv', // sets output filename
    'keep_result' => false,              // do not keep result for getResult()
]));
```

With `TwigAdapter`, you can pass the exported data to a Twig template for further processing. This class requires the `Twig_Environment` instance as the first constructor argument.

```php
$twig = $this->get('twig'); // retrieve the Twig_Environment instance from the service container
$exporter->setOutputAdapter(new TwigAdapter($twig, [
    'template' => '@AppBundle/exporter/my_exporter_template.html.twig',
]));
```

Note that the default template located at `Resources/view/exporter/template.html.twig` will be used if no template is given. You can use this file to learn writing your own template.

Hint: You can define your own output adapter services. For example, you can define a service for `TwigAdapter` that uses your own template. After that the above code example can be simplified as follow:
 
```
$exporter->setOutputAdapter($this->get('app.exporter.output_adapter.twig'));
```

Here is how the service is defined in `services.yml`:

```php
app.exporter.output_adapter.twig:
    class: Sparkson\DataExporterBundle\Exporter\OutputAdapter\TwigAdapter
    arguments:
        - @twig
        - { template: "@AppBundle/exporter/my_exporter_template.html.twig" }
```

## Misc. notes

### Changing column properties

By default, columns are exported in the order they were added. But you may want to modify the column order after the exporter is built. Here's how:
```php
$columnSet = $exporter->getColumns();
$columnSet->getChild('lastName')->setPosition(2);
```
Note that `position` is merely a sorting hint. The library will not modify other columns' positions for uniqueness. 

Alternatively, you may re-order the columns via `setColumnOrders()`:

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

This is especially useful when you provide an UI for users to choose which columns to export. Instead of adding ifs around the builder's add field statements, just add all of them first and disable the unwanted ones after the exporter is built. 

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

We can adopt this technique using PHP's [generator](http://php.net/manual/en/language.generators.overview.php), like so:
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