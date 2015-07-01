<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

    public function getValue($value, array $options)
    {
        $callable = $options['callback'];

        return call_user_func_array($callable, array(
            $value,
            $options['callback_options'],
        ));
    }

    public function getName()
    {
        return 'callback';
    }
}