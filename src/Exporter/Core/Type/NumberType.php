<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A number type.
 *
 * The following additional options are available:
 * - decimals: Number of decimal points, defaults to "2"
 * - dec_point: String representing the decimal point, defaults to "."
 * - thousands_sep: String representing the thousand separator, defaults to ","
 *
 * The value and the above options will be passed to PHP's number_format function to
 * format the field value.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class NumberType extends AbstractSimpleType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'decimals' => 2,
            'dec_point' => '.',
            'thousands_sep' => ',',
            'compound' => false,
        ));

        $resolver->setAllowedTypes('decimals', array('int'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'number';
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($value, $options)
    {
        return number_format($value, $options['decimals'], $options['dec_point'], $options['thousands_sep']);
    }

}