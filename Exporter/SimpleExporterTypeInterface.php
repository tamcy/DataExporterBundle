<?php

namespace Sparkson\DataExporterBundle\Exporter;


interface SimpleExporterTypeInterface extends ExporterTypeInterface
{
    public function getValue($value, array $options);
}