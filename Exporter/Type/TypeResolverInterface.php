<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


interface TypeResolverInterface
{

    /** @return ExporterTypeInterface */
    public function getType($name);

}