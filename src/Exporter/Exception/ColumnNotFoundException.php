<?php

namespace Sparkson\DataExporterBundle\Exporter\Exception;

/**
 * Thrown when a requested column could not be found.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class ColumnNotFoundException extends \Exception
{
    public function __construct($columnName, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Column name %s not found!', $columnName), $code, $previous);
    }
}