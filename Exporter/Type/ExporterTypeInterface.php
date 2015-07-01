<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExporterTypeInterface
{
    public function setDefaultOptions(OptionsResolver $resolver);

//    public function getValue($data, $fieldName, array $options);

    public function getName();

    public function buildExporter(ExporterBuilder $builder);

    public function getValue(ColumnValueResolverInterface $valueResolver, $data, $fieldName, array $options);

}