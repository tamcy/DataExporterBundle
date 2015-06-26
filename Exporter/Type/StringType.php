<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        ));

        $resolver->setAllowedTypes('format', array('null', 'string'));
    }

    public function getName()
    {
        return 'string';
    }

    public function getValue($value, array $options)
    {
        return $options['format'] ? sprintf($options['format'], $value) : $value;
    }

}