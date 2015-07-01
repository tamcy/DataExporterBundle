<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType implements ExporterTypeInterface
{
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
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => null,
            'writer_options' => array(),
            'translation_domain' => null,
            'compound' => true,
            'filters' => array(),
            'property_path' => null,
        ));

        $resolver->setAllowedTypes('writer_options', 'array');
        $resolver->setAllowedTypes('compound', 'bool');
        $resolver->setAllowedTypes('filters', 'array');
    }

    public function getName()
    {
        return 'exporter';
    }

    public function getValue(ColumnValueResolverInterface $valueResolver, $data, $fieldName, array $options)
    {
        $propertyPath = $options['property_path'] ?: $fieldName;

        return $valueResolver->getValue($data, $propertyPath, $options);
    }
}