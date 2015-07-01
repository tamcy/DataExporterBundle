<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver;


use Sparkson\DataExporterBundle\Exporter\Column\Column;

interface ColumnValueResolverInterface
{
    public function getValue($data, Column $column);
}