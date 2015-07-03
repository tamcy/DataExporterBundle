<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Exception\InvalidOperationException;
use Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface;
use Sparkson\DataExporterBundle\Exporter\Type\TypeResolverInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;

/**
 * The exporter builder.
 *
 * Normally you don't initialize this class by yourself, but create one via
 * ExporterFactory::createBuilder() or ExporterFactory::createExporter().
 */
class ExporterBuilder
{
    /**
     * @var TypeResolverInterface
     */
    private $typeResolver;

    /**
     * @var ColumnValueResolverInterface
     */
    private $valueResolver;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var ExporterTypeInterface
     */
    private $rootType = null;

    private $locked = false;

    public function __construct(TypeResolverInterface $typeResolver,
                                ColumnValueResolverInterface $valueResolver,
                                $rootType = null)
    {
        $this->fields = array();
        $this->typeResolver = $typeResolver;
        $this->valueResolver = $valueResolver;
        $this->rootType = $rootType;
        $this->locked = ($rootType !== null);
    }

    public function add($name, $type = null, array $options = array())
    {
        if ($this->locked) {
            throw new InvalidOperationException('This builder has a root type, so you cannot add additional field to it');
        }

        $this->fields[$name] = array(
            'type' => $type,
            'options' => $options,
        );
        return $this;
    }

    public function setValueResolver(ColumnValueResolverInterface $resolver)
    {
        $this->valueResolver = $resolver;
        return $this;
    }

    private function getResolveColumns()
    {
        $columns = new ColumnSet();

        if ($this->rootType) {
            $this->locked = false;
            $type = $this->typeResolver->getType($this->rootType);
            $type->buildExporter($this);
        }

        foreach ($this->fields as $columnName => $columnData) {

            $type = $columnData['type'];
            if ($type === null) {
                $type = 'string';
            }

            if (!$type instanceof ExporterTypeInterface) {
                $type = $this->typeResolver->getType($type);
            }

            $column = new Column($columnName, $type, $columnData['options']);
            $options = $column->getOptions();
            if ($options['compound']) {
                $builder = new ExporterBuilder($this->typeResolver, $this->valueResolver);
                $type->buildExporter($builder);
                $column->setChildren($builder->getResolveColumns()->getChildren());
            }

            $columns->addChild($column);
        }
        return $columns;
    }

    public function getExporter()
    {
        $instance = new Exporter($this->valueResolver);
        $instance->setColumns($this->getResolveColumns());

        return $instance;
    }
}