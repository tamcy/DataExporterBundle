<?php

namespace Sparkson\DataExporterBundle\Tests\Exporter\Fixtures;

use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Sparkson\DataExporterBundle\Exporter\Type\AbstractType;

class ProfileType extends AbstractSimpleType
{
    public function buildExporter(ExporterBuilder $builder)
    {
        $builder
            ->add('firstName', 'string')
            ->add('lastName', 'string')
            ->add('address', 'test_address');
    }

    public function getName()
    {
        return 'test_profile';
    }

}