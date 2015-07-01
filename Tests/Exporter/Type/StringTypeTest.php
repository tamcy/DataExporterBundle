<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\Core\Type\StringType;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\SimpleTypeColumnValueResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringTypeTest extends \PHPUnit_Framework_TestCase
{
    private $valueResolver;

    public function setUp()
    {
        $this->valueResolver = new SimpleTypeColumnValueResolver();
    }

    public function testValueReturnedCorrectly()
    {
        $optionsResolver = new OptionsResolver();

        $stringType = new StringType();
        $stringType->configureOptions($optionsResolver);

        $this->assertEquals('foo', $stringType->getValue($this->valueResolver, array('property' => 'foo'), '[property]', $optionsResolver->resolve(array())));
        $this->assertEquals('bar', $stringType->getValue($this->valueResolver, array('property' => 'bar'), '[property]', $optionsResolver->resolve(array())));
    }

    public function testValueFormattedCorrectly()
    {
        $optionsResolver = new OptionsResolver();

        $stringType = new StringType();
        $stringType->configureOptions($optionsResolver);

        $this->assertEquals('Name: foo', $stringType->getValue($this->valueResolver, array('property' => 'foo'), '[property]', $optionsResolver->resolve(array('format' => 'Name: %s'))));
    }
}