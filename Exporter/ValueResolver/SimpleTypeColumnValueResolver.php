<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver;

use Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter\FilterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * The default value resolver.
 */
class SimpleTypeColumnValueResolver implements ColumnValueResolverInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($data, $propertyPath, $options)
    {
        $rawValue = $this->propertyAccessor->getValue($data, $propertyPath);

        if (!empty($options['filters']) && is_array($options['filters'])) {
            foreach ($options['filters'] as $filter) {
                if ($filter instanceof FilterInterface) {
                    $rawValue = $filter->filterValue($rawValue);
                } else {
                    // Assume simple function that accepts value as the first parameter
                    $rawValue = call_user_func($filter, $rawValue);
                }
            }
        }
        return $rawValue;
    }
}