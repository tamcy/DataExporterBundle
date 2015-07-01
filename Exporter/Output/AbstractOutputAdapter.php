<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractOutputAdapter implements OutputInterface
{
    protected $options;

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }


    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }
}