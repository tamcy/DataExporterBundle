<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\ColumnValueResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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

    public function getValue($data, Column $column)
    {
        $options = $column->getOptions();
        $propertyPath = $options['property_path'] ?: $column->getName();

        $rawValue = $this->propertyAccessor->getValue($data, $propertyPath);

        if ($options['filters']) {
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