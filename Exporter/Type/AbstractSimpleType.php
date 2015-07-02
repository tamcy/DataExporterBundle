<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;

use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base class for simple types.
 *
 * This class abstracts the details of retrieving the value using the ColumnValueResolverInterface,
 * so that descendant classes only need to take care of the value transformation part via implemting
 * the processValue() method.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
abstract class AbstractSimpleType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(ColumnValueResolverInterface $valueResolver, $data, $fieldName, array $options)
    {
        return $this->processValue(parent::getValue($valueResolver, $data, $fieldName, $options), $options);
    }

    /**
     * Transforms the field value.
     *
     * @param mixed $value Source field value
     * @param array $options The options
     * @return mixed Transformed value for output
     */
    abstract protected function processValue($value, $options);
}