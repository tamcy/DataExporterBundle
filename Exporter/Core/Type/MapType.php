<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
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
            'compound' => false,
        ));

        $resolver->setAllowedTypes('map', array('null', 'array', '\Traversable'));
    }

    public function getName()
    {
        return 'map';
    }

    protected function processValue($value, $options)
    {
        if (isset($options['map'][$value])) {
            return $options['map'][$value];
        }

        return $options['map']['default_value'];
    }

}