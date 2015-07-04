<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver;

/**
 * Interface for value resolvers.
 *
 * A value resolver is responsible for resolving the value corresponding to the column from $data.
 * The resolved value will then be passed to the belonging type to return the final, exported value for output.
 */
interface ValueResolverInterface
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