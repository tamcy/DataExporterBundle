<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A datetime field type.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class DateTimeType extends AbstractSimpleType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'empty_value' => '',
            'format' => 'Y-m-d H:i:s',
            'timezone' => null,
            'source_format' => null,
            'compound' => false,
        ));

        $resolver->setAllowedTypes('timezone', array('null', '\DateTimeZone'));

    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($value, $options)
    {
        if (!$value) {
            return $options['empty_value'];
        }

        if (!$value instanceof \DateTime) {
            if ($options['source_format']) {
                $value = \DateTime::createFromFormat($options['source_format'], $value);
            } else {
                $value = new \DateTime($value, $options['timezone']);
            }
        }

        return $value->format($options['format']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'datetime';
    }
}