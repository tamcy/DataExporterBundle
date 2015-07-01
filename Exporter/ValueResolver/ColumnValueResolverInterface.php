<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver;


interface ColumnValueResolverInterface
{
    public function getValue($data, $propertyPath, $options);
}