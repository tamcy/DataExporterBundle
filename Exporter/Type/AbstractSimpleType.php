<?php

namespace Sparkson\DataExporterBundle\Exporter\Type;


use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSimpleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }

    abstract protected function processValue($value, $options);

    public function getValue(ColumnValueResolverInterface $valueResolver, $data, $fieldName, array $options)
    {
        return $this->processValue(parent::getValue($valueResolver, $data, $fieldName, $options), $options);
    }
}