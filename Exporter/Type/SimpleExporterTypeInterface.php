<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


interface SimpleExporterTypeInterface extends ExporterTypeInterface
{
    public function getValue($value, array $options);
}