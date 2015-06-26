<?php

namespace Sparkson\DataExporterBundle\Exporter;


interface ComplexExporterTypeInterface extends ExporterTypeInterface
{
    public function getValue($data, $fieldName, array $options);
}