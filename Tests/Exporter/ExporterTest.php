<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Core\Type\RawType;
use Sparkson\DataExporterBundle\Exporter\Core\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\Exporter;
use Sparkson\DataExporterBundle\Exporter\Output\CSVAdapter;

class ExporterTest extends \PHPUnit_Framework_TestCase
{

    private $dataSet1 = array(
        array('firstName' => 'Foo', 'lastName' => 'Chan', 'address' => ['room' => 'B', 'floor' => '12']),
        array('firstName' => 'Bar', 'lastName' => 'Wong', 'address' => ['room' => 'A', 'floor' => '14']),
    );

    public function testSimpleStructure()
    {
        $columns = new ColumnSet();
        $columns->addChild(new Column('firstName', new StringType(), array('property_path' => '[firstName]')));
        $columns->addChild(new Column('lastName', new StringType(), array('property_path' => '[lastName]')));

        $exporter = new Exporter();
        $exporter
            ->setColumns($columns)
            ->setOutput(new CSVAdapter())
            ->setData($this->dataSet1)
            ->execute();
        $result = $exporter->getResult();

        $this->assertEquals('"First Name","Last Name"
Foo,Chan
Bar,Wong
', $result);
    }

    public function testNestedStructure()
    {
        $columns = new ColumnSet();
        $columns->addChild(new Column('firstName', new StringType(), array('property_path' => '[firstName]')));
        $columns->addChild(new Column('lastName', new StringType(), array('property_path' => '[lastName]')));

        $addressColumn = new Column('address', new RawType(), array('property_path' => '[address]'));
        $addressColumn->addChild(new Column('room', new StringType(), array('property_path' => '[room]')));
        $addressColumn->addChild(new Column('floor', new StringType(), array('property_path' => '[floor]')));
        $columns->addChild($addressColumn);

        $exporter = new Exporter();
        $exporter
            ->setColumns($columns)
            ->setOutput(new CSVAdapter())
            ->setData($this->dataSet1)
            ->execute();
            $result = $exporter->getResult();

        $this->assertEquals('"First Name","Last Name",Room,Floor
Foo,Chan,B,12
Bar,Wong,A,14
', $result);
    }

    public function testNonDefaultColumnOrders()
    {
        $columns = new ColumnSet();
        $columns->addChild(new Column('firstName', new StringType(), array('property_path' => '[firstName]')));
        $columns->addChild(new Column('lastName', new StringType(), array('property_path' => '[lastName]')));
        $columns->setColumnOrders(array('lastName', 'firstName'));

        $exporter = new Exporter();
        $exporter
            ->setColumns($columns)
            ->setOutput(new CSVAdapter())
            ->setData($this->dataSet1)
            ->execute();
        $result = $exporter->getResult();

        $this->assertEquals('"Last Name","First Name"
Chan,Foo
Wong,Bar
', $result);
    }

}