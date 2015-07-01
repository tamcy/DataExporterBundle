<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

    public function getName()
    {
        return 'number';
    }

    public function getValue($value, array $options)
    {
        return number_format($value, $options['decimals'], $options['dec_point'], $options['thousands_sep']);
    }

}