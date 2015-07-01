<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractSimpleType;
use Sparkson\DataExporterBundle\Exporter\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RawType extends AbstractSimpleType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'compound' => false,
        ));
    }

    public function getName()
    {
        return 'raw';
    }

    public function getValue($value, array $options)
    {
        return $value;
    }
}