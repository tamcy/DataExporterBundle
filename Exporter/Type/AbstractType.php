<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\ExporterBuilder;
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
        ));

        $resolver->setAllowedTypes('writer_options', 'array');
        $resolver->setAllowedTypes('compound', 'bool');
    }

    public function getName()
    {
        return 'exporter';
    }

}