<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSimpleType extends AbstractType implements SimpleExporterTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'property_path' => null,
            'filters' => array(),
        ));

        $resolver->setAllowedTypes('filters', 'array');
    }

    public function getValue($value, array $options)
    {
        return $value;
    }

}