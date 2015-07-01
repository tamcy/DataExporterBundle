<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


use Sparkson\DataExporterBundle\Exporter\Exception\TypeNotFoundException;

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
        throw new TypeNotFoundException(sprintf('Exporter type %s not found.', $name));
    }
}