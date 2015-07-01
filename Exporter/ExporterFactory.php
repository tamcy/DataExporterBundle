<?php

namespace Sparkson\DataExporterBundle\Exporter;


use Sparkson\DataExporterBundle\Exporter\Type\TypeResolverInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;

class ExporterFactory
{
    /**
     * @var TypeResolverInterface
     */
    private $typeResolver;
    /**
     * @var ColumnValueResolverInterface
     */
    private $valueResolver;

    public function __construct(TypeResolverInterface $typeResolver,
                                ColumnValueResolverInterface $valueResolver)
    {
        $this->typeResolver = $typeResolver;
        $this->valueResolver = $valueResolver;
    }

    public function createBuilder()
    {
        return new ExporterBuilder($this->typeResolver, $this->valueResolver);
    }

    public function createExporter($type)
    {
        return (new ExporterBuilder($this->typeResolver, $this->valueResolver, $type))->getExporter();
    }

}