<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\Exception\TypeNotFoundException;

/**
 * Interface for resolving a type.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
interface TypeResolverInterface
{

    /**
     * Resolves a field type by the supplied type name.
     *
     * @param string $name Name of the field type
     * @return ExporterTypeInterface The resolved field type
     * @throws TypeNotFoundException When a field type could not be found
     */
    public function getType($name);

}