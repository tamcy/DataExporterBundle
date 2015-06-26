<?php

namespace Sparkson\DataExporterBundle\Exporter;


use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class SimpleTypeRawValueResolver implements  ColumnValueResolverInterface
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

//        if ($options['filters']) {
//            foreach ($options['filters'] as $filter => $filterOptions) {
//
//            }
//        }
        return $rawValue;
    }
}