<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A callback field type.
 *
 * You can define your own callback function via the option's `callback` parameter.
 * The callback expects the first argument to be the field value. An optional
 * `callback_options` can de defined in the options which will be passed to the
 * callback function as the second argument.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class CallbackType extends AbstractSimpleType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'callback' => null,
            'callback_options' => array(),
            'compound' => false,
        ));

        $resolver->setRequired('callback');
        $resolver->setAllowedTypes('callback', array('callable'));
        $resolver->setAllowedTypes('callback_options', array('array'));
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($value, $options)
    {
        $callable = $options['callback'];

        return call_user_func_array($callable, array(
            $value,
            $options['callback_options'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'callback';
    }
}