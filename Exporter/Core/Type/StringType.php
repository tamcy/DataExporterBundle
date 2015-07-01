<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
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
            'compound' => false,
        ));

        $resolver->setAllowedTypes('format', array('null', 'string'));
    }

    public function getName()
    {
        return 'string';
    }

    protected function processValue($value, $options)
    {
        return $options['format'] ? sprintf($options['format'], $value) : $value;
    }

}