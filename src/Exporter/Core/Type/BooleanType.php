<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A boolean field type.
 *
 * This field type expects the field value to be a boolean.
 * If the value is TRUE, it returns the option's `true_value` for output.
 * If the value is FALSE, it returns the option's `false_value` for output.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
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

    /**
     * {@inheritdoc}
     */
    protected function processValue($value, $options)
    {
        if ($value) {
            return $options['true_value'];
        }
        return $options['false_value'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'boolean';
    }
}