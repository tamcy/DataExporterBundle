<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface;
use Sparkson\DataExporterBundle\Exporter\Type\TypeResolverInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ValueResolverInterface;

/**
 * Exporter factory class.
 */
class ExporterFactory
{
    /**
     * @var TypeResolverInterface
     */
    private $typeResolver;
    /**
     * @var ValueResolverInterface
     */
    private $valueResolver;

    /**
     * Class constructor.
     *
     * @param TypeResolverInterface $typeResolver
     * @param ValueResolverInterface $valueResolver
     */
    public function __construct(TypeResolverInterface $typeResolver,
                                ValueResolverInterface $valueResolver)
    {
        $this->typeResolver = $typeResolver;
        $this->valueResolver = $valueResolver;
    }

    /**
     * Creates a new exporter builder.
     *
     * @return ExporterBuilder
     */
    public function createBuilder()
    {
        return new ExporterBuilder($this->typeResolver, $this->valueResolver);
    }

    /**
     * Creates an exporter with the supplied type as the root type.
     *
     * $type can be a name registered in the type registry, or an instance of
     * ExporterTypeInterface.
     *
     * @param string|ExporterTypeInterface $type
     * @return Exporter
     */
    public function createExporter($type)
    {
        return (new ExporterBuilder($this->typeResolver, $this->valueResolver, $type))->getExporter();
    }

}