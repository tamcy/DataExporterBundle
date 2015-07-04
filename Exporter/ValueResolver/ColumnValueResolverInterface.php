<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver;

/**
 * Interface for value resolvers.
 */
interface ColumnValueResolverInterface
{
    /**
     * Retrieves the value from $data using information provided by $propertyPath and $options.
     *
     * @param mixed $data
     * @param string $propertyPath
     * @param array $options
     * @return mixed
     */
    public function getValue($data, $propertyPath, $options);

}