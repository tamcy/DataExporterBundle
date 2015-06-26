<?php

namespace Sparkson\DataExporterBundle\Exporter;


use Sparkson\DataExporterBundle\Exporter\Column\Column;

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
    private $columns;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var mixed
     */
    private $data = null;

    public function __construct(TypeResolverInterface $typeResolver,
                                ColumnValueResolverInterface $valueResolver,
                                $data = null)
    {
        $this->columns = array();
        $this->typeResolver = $typeResolver;
        $this->valueResolver = $valueResolver;
        $this->data = $data;
    }

    public function add($name, $type = null, array $options = array())
    {
        $this->columns[$name] = array(
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

    /**
     * @param WriterInterface $writer
     * @return $this
     */
    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
        return $this;
    }

    public function getExporter()
    {
        $instance = new Exporter($this->valueResolver);

        foreach ($this->columns as $columnName => $columnData) {
            $type = $columnData['type'];
            if ($type === null) {
                $type = 'string';
            }

            if ($type instanceof ExporterTypeInterface) {
                $type = $columnData;
            } else {
                $type = $this->typeResolver->getType($type);
                if (!$type) {
                    throw new \Exception('Unable to resolve exporter type ' . $type);
                }
            }

            $instance->add(new Column($columnName, $type, $columnData['options']));
        }

        $instance->setWriter($this->writer);

        if ($this->data) {
            $instance->setData($this->data);
        }

        return $instance;
    }
}