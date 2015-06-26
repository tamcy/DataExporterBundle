<?php

namespace Sparkson\DataExporterBundle\Exporter;


use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType implements ExporterTypeInterface
{
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
            'property_path' => null,
            'label' => null,
            'writer_options' => array(),
            'translation_domain' => null,
        ));

        $resolver->setAllowedTypes('writer_options', 'array');
    }
}