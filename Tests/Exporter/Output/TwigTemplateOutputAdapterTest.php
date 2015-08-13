<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Core\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\Exporter;
use Sparkson\DataExporterBundle\Exporter\Output\TwigTemplateOutputAdapter;

class TwigTemplateOutputAdapterTest extends \PHPUnit_Framework_TestCase {

    private $dataSet = array(
        array('firstName' => 'Foo', 'lastName' => 'Chan'),
        array('firstName' => 'Bar', 'lastName' => 'Wong'),
    );

    public function testSimpleStructure()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../../Resources/view');
        $twig = new \Twig_Environment($loader, array());

        $columns = new ColumnSet();
        $columns->addChild(new Column('firstName', new StringType(), array('property_path' => '[firstName]')));
        $columns->addChild(new Column('lastName', new StringType(), array('property_path' => '[lastName]')));

        $exporter = new Exporter();
        $exporter
            ->setColumns($columns)
            ->setOutput(new TwigTemplateOutputAdapter($twig, ['template' => 'template.html.twig']))
            ->setDataSet($this->dataSet)
            ->execute();

        $result = $exporter->getResult();
        $this->assertEquals('<table><thead><tr><th>First Name</th><th>Last Name</th></tr></thead><tbody><tr><td>Foo</td><td>Chan</td></tr><tr><td>Bar</td><td>Wong</td></tr></tbody></table>', $result);
    }

}