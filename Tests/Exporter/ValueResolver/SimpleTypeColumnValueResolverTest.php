<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter\ValueResolver;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Core\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter\CustomFilter;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\SimpleTypeColumnValueResolver;

class SimpleTypeColumnValueResolverTest extends \PHPUnit_Framework_TestCase
{
    private function newObject()
    {
        return new SimpleTypeColumnValueResolver();
    }

    public function testResolveObjectValue()
    {
        $column = new Column('name', new StringType());

        $obj = new \stdClass();
        $obj->name = 'Foo Bar';

        $this->assertEquals('Foo Bar', $this->newObject()->getValue($obj, $column));
    }

    public function testResolveValueWithSimpleFilter()
    {
        $column = new Column('name', new StringType(), array(
            'filters' => array('trim'),
        ));

        $obj = new \stdClass();
        $obj->name = '   Foo Bar   ';
        $this->assertEquals('Foo Bar', $this->newObject()->getValue($obj, $column));
    }

    public function testResolveValueWithCustomFilter()
    {
        $column = new Column('name', new StringType(), array(
            'filters' => array(
                'trim',
                new CustomFilter(function ($value) {
                    return str_replace('Foo', 'Baz', $value);
                })),
        ));

        $obj = new \stdClass();
        $obj->name = '   Foo Bar   ';
        $this->assertEquals('Baz Bar', $this->newObject()->getValue($obj, $column));
    }

}