<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


interface ComplexExporterTypeInterface extends ExporterTypeInterface
{
    public function getValue($data, $fieldName, array $options);
}