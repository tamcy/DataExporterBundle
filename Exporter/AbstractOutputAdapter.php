<?php

namespace Sparkson\DataExporterBundle\Exporter;


use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractOutputAdapter implements OutputInterface
{
    protected $options;

    protected $columns;

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
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