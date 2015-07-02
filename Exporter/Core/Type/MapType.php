<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A array map field type.
 *
 * This type accepts a mapping array $map. The field value $value will be used as the key to
 * transform itself to the value of the mapping, i.e. $value -> $map[$value].
 *
 * The following additional options are available:
 * - map: The mapping array.
 * - default_value: A string which will be returned when the mapping does not exist.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'map';
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($value, $options)
    {
        if (isset($options['map'][$value])) {
            return $options['map'][$value];
        }

        return $options['map']['default_value'];
    }

}