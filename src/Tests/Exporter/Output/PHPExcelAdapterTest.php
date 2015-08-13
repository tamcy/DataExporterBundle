<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Core\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\Exporter;
use Sparkson\DataExporterBundle\Exporter\Output\PHPExcelAdapter;

class PHPExcelAdapterTest extends \PHPUnit_Framework_TestCase
{
    private $dataSet = array(
        array('firstName' => 'Foo', 'lastName' => 'Chan'),
        array('firstName' => 'Bar', 'lastName' => 'Wong'),
    );

    public function testSimpleStructure()
    {
        $columns = new ColumnSet();
        $columns->addChild(new Column('firstName', new StringType(), array('property_path' => '[firstName]')));
        $columns->addChild(new Column('lastName', new StringType(), array('property_path' => '[lastName]')));

        $exporter = new Exporter();
        $exporter
            ->setColumns($columns)
            ->setOutput(new PHPExcelAdapter(array(
                'writer' => 'CSV',
            )))
            ->setDataSet($this->dataSet);

        $exporter->execute();
        $result = preg_replace("#\r|\n#", '', $exporter->getResult());
        $this->assertEquals('"First Name","Last Name""Foo","Chan""Bar","Wong"', $result);
    }
}