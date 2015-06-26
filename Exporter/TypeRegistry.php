<?php

namespace Sparkson\DataExporterBundle\Exporter;


class TypeRegistry implements TypeResolverInterface
{
    private $types;

    public function __construct()
    {
        $this->types = array();
    }

    public function addType(ExporterTypeInterface $type)
    {
        $this->types[$type->getName()] = $type;
    }

    public function getType($name)
    {
        if (isset($this->types[$name])) {
            return $this->types[$name];
        }
    }
}