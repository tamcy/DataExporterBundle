<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExporterTypeInterface
{
    public function setDefaultOptions(OptionsResolver $resolver);

//    public function getValue($data, $fieldName, array $options);

    public function getName();

    public function buildExporter(ExporterBuilder $builder);

}