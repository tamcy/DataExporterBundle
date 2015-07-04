<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter;

/**
 * Interface for value filters.
 */
interface FilterInterface
{

    /**
     * Filters a value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function filterValue($value);

}