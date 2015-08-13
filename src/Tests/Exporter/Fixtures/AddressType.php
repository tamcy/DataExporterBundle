<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter\Fixtures;


use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\Type\AbstractType;

class AddressType extends AbstractType
{
    public function buildExporter(ExporterBuilder $builder)
    {
        $builder
            ->add('room', 'string')
            ->add('floor', 'string')
            ->add('block', 'string');
    }

    public function getName()
    {
        return 'test_address';
    }

}