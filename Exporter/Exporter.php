<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnCollection;
use Sparkson\DataExporterBundle\Exporter\Exception\InvalidArgumentException;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\SimpleTypeColumnValueResolver;

class Exporter
{
    /**
     * @var ColumnCollection
     */
    private $columns;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var array
     */
    private $data;

    /**
     * @var ColumnValueResolverInterface
     */
    private $valueResolver;


    public function __construct(ColumnValueResolverInterface $valueResolver = null)
    {
        $this->columns = new ColumnCollection();
        $this->valueResolver = $valueResolver ?: new SimpleTypeColumnValueResolver();
    }

    public function add(Column $column)
    {
        $this->columns->add($column);
        return $this;
    }

    public function get($columnName)
    {
        return $this->columns->get($columnName);
    }

    public function has($columnName)
    {
        return $this->columns->has($columnName);
    }

    public function remove($columnName)
    {
        $this->columns->remove($columnName);
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return WriterInterface
     */
    public function getWriter()
    {
        return $this->writer;
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

    public function execute()
    {
        $columns = $this->columns->getSortedActiveColumns();

        if (!is_array($this->data) && !$this->data instanceof \Traversable) {
            throw new InvalidArgumentException('The supplied data is not traversable.');
        }

        $this->writer->setColumns(array_map(function (Column $column) {
            return $column->getLabel();
        }, $columns));

        $this->writer->begin();

        foreach ($this->data as $idx => $a) {
            $this->writer->beginRow();

            foreach ($columns as $pos => $column) {
                $options = $column->getOptions();
                $columnType = $column->getType();

                if ($columnType instanceof SimpleExporterTypeInterface) {
                    $rawValue = $this->valueResolver->getValue($a, $column, $options);
                    $value = $columnType->getValue($rawValue, $options);
                } elseif ($columnType instanceof ComplexExporterTypeInterface) {
                    $value = $columnType->getValue($a, $column->getName(), $options);
                } else {
                    throw new InvalidArgumentException('Column type must either implement SimpleExporterTypeInterface or ComplexExporterTypeInterface');
                }

                $this->writer->writeColumn($value, $options['writer_options']);
            }

            $this->writer->endRow();
        }

        $this->writer->end();
        return $this;
    }

    public function getResult()
    {
        return $this->writer->getResult();
    }

}