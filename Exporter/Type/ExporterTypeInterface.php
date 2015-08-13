<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ValueResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface for an exporter field type.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
interface ExporterTypeInterface
{
    /**
     * Sets the default options for this exporter type.
     *
     * @param OptionsResolver $resolver
     * @return mixed
     */
    public function setDefaultOptions(OptionsResolver $resolver);

    /**
     * Returns the name of this type.
     *
     * @return string
     */
    public function getName();

    /**
     * Builds the exporter.
     *
     * Adds additional fields to the exporter.
     *
     * @param ExporterBuilder $builder The exporter builder
     */
    public function buildExporter(ExporterBuilder $builder);

    /**
     * Transforms the field value to the one as defined in the types.
     *
     * @param ValueResolverInterface $valueResolver
     * @param mixed                  $data The source data
     * @param string                 $fieldName The requested field name
     * @param array                  $options The options
     * @return mixed The transformed value
     */
    public function getValue(ValueResolverInterface $valueResolver, $data, $fieldName, array $options);

}