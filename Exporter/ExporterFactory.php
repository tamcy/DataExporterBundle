<?php

namespace Sparkson\DataExporterBundle\Exporter;


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

    public function createBuilder($data = null)
    {
        return new ExporterBuilder($this->typeResolver, $this->valueResolver, $data);
    }

}