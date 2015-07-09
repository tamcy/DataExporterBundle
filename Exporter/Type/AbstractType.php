<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ValueResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The abstract type class implemting ExporterTypeInterface.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
abstract class AbstractType implements ExporterTypeInterface
{

    /**
     * {@inheritdoc}
     */
    public function buildExporter(ExporterBuilder $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => true,
            'property_path' => null,
            'resolver_options' => array(), // column options specified for the value resolver
        ));

        $resolver->setAllowedTypes('compound', 'bool');
        $resolver->setAllowedTypes('resolver_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'exporter';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(ValueResolverInterface $valueResolver, $data, $fieldName, array $options)
    {
        $propertyPath = $options['property_path'] ?: $fieldName;

        return $valueResolver->getValue($data, $propertyPath, $options['resolver_options']);
    }
}