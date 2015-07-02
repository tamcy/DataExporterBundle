<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A string field type that transforms the field value to string.
 *
 * An optional "format" value can be supplied to the options array.
 * When set, the value will be formatted using sprintf() function.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class StringType extends AbstractSimpleType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'format' => null,
            'compound' => false,
        ));

        $resolver->setAllowedTypes('format', array('null', 'string'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'string';
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($value, $options)
    {
        return $options['format'] ? sprintf($options['format'], $value) : (string)$value;
    }

}