<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\Type\StringType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testValueReturnedCorrectly()
    {
        $optionsResolver = new OptionsResolver();

        $stringType = new StringType();
        $stringType->configureOptions($optionsResolver);

        $this->assertEquals('foo', $stringType->getValue('foo', $optionsResolver->resolve(array())));
        $this->assertEquals('bar', $stringType->getValue('bar', $optionsResolver->resolve(array())));
    }

    public function testValueFormattedCorrectly()
    {
        $optionsResolver = new OptionsResolver();

        $stringType = new StringType();
        $stringType->configureOptions($optionsResolver);

        $this->assertEquals('Name: foo', $stringType->getValue('foo', $optionsResolver->resolve(array('format' => 'Name: %s'))));
    }
}