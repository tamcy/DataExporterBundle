<?php

namespace Sparkson\DataExporterBundle\Exporter;


use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExporterTypeInterface
{
    public function setDefaultOptions(OptionsResolver $resolver);

    public function getName();
}