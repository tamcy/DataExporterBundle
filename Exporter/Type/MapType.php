<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends AbstractSimpleType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'default_value' => '',
            'map' => array(),
        ));

        $resolver->setAllowedTypes('map', array('null', 'array', '\Traversable'));
    }

    public function getName()
    {
        return 'map';
    }

    public function getValue($value, array $options)
    {
        if (isset($options['map'][$value])) {
            return $options['map'][$value];
        }

        return $options['map']['default_value'];
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        // TODO: Implement setDefaultOptions() method.
    }
}