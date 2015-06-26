<?php

namespace Sparkson\DataExporterBundle\Exporter;


use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSimpleType extends AbstractType implements SimpleExporterTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'property_path' => null,
        ));
    }
}