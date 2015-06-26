<?php

namespace Sparkson\DataExporterBundle\Exporter;


interface TypeResolverInterface
{

    public function getType($name);

}