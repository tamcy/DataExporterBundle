<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Exception\InvalidOperationException;
use Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface;
use Sparkson\DataExporterBundle\Exporter\Type\TypeResolverInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ValueResolverInterface;

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
    protected $typeResolver;

    /**
     * @var ValueResolverInterface
     */
    protected $valueResolver;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var ExporterTypeInterface
     */
    protected $rootType = null;

    protected $locked = false;

    /**
     * Class constructor.
     *
     * @param TypeResolverInterface $typeResolver
     * @param ValueResolverInterface $valueResolver
     * @param string|ExporterTypeInterface $rootType
     */
    public function __construct(TypeResolverInterface $typeResolver,
                                ValueResolverInterface $valueResolver,
                                $rootType = null)
    {
        $this->fields = array();
        $this->typeResolver = $typeResolver;
        $this->valueResolver = $valueResolver;
        $this->rootType = $rootType;
        $this->locked = ($rootType !== null);
    }

    /**
     * Adds a field to the builder.
     *
     * If a type name is provided, the type will be retrieved from the type registry.
     * Field type will be wrapped by a column instance.
     *
     * @param string $name The name of the field/column, which should be unique at the same level
     * @param string|ExporterTypeInterface $type The exporter type name or instance
     * @param array $options Field options
     * @return $this
     * @throws InvalidOperationException When a root type is defined
     */
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

    /**
     * Sets the value resolver to be used.
     *
     * @param ValueResolverInterface $resolver
     * @return $this
     */
    public function setValueResolver(ValueResolverInterface $resolver)
    {
        $this->valueResolver = $resolver;
        return $this;
    }

    protected function getResolveColumns()
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

    /**
     * Builds and returns the exporter instance.
     *
     * @return Exporter
     */
    public function getExporter()
    {
        $instance = new Exporter($this->valueResolver);
        $instance->setColumns($this->getResolveColumns());

        return $instance;
    }
}