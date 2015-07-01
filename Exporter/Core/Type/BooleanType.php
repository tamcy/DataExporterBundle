<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
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
            'compound' => false,
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