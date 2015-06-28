<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter;


use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Exporter;
use Sparkson\DataExporterBundle\Exporter\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\Writer\CSVWriter;

class ExporterTest extends \PHPUnit_Framework_TestCase
{

    private $dataSet1 = array(
        array('firstName' => 'Foo', 'lastName' => 'Chan'),
        array('firstName' => 'Bar', 'lastName' => 'Wong'),
    );

    public function testOutput()
    {
        $exporter = new Exporter();
        $exporter
            ->add(new Column('firstName', new StringType(), array('property_path' => '[firstName]')))
            ->add(new Column('lastName', new StringType(), array('property_path' => '[lastName]')))
            ->setWriter(new CSVWriter())
            ->setData($this->dataSet1)
            ->execute();
        $result = $exporter->getResult();

        $this->assertEquals('"First Name","Last Name"
Foo,Chan
Bar,Wong
', $result);
    }
}