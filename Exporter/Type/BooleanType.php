<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanType extends AbstractSimpleType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'true_value' => 'True',
            'false_value' => 'False',
        ));
    }

    public function getValue($value, array $options)
    {
        if ($value) {
            return $options['true_value'];
        }
        return $options['false_value'];
    }

    public function getName()
    {
        return 'boolean';
    }
}