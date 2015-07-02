<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\Exception\TypeNotFoundException;

/**
 * Type registry.
 *
 * Exporter field types that registered in this registry can be retrieved
 * by the exporter builder by the types' names.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class TypeRegistry implements TypeResolverInterface
{

    /**
     * @var ExporterTypeInterface[] array of registered field types.
     */
    private $types;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->types = array();
    }

    /**
     * Registers a field type.
     *
     * @param ExporterTypeInterface $type
     */
    public function addType(ExporterTypeInterface $type)
    {
        $this->types[$type->getName()] = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($name)
    {
        if (isset($this->types[$name])) {
            return $this->types[$name];
        }

        throw new TypeNotFoundException(sprintf('Field type %s not found.', $name));
    }
}