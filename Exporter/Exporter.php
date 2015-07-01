<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnCollection;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnCollectionInterface;
use Sparkson\DataExporterBundle\Exporter\Exception\InvalidArgumentException;
use Sparkson\DataExporterBundle\Exporter\Output\OutputInterface;
use Sparkson\DataExporterBundle\Exporter\Type\ComplexExporterTypeInterface;
use Sparkson\DataExporterBundle\Exporter\Type\SimpleExporterTypeInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\SimpleTypeColumnValueResolver;

class Exporter
{
    /**
     * @var ColumnCollectionInterface
     */
    private $columns;

    /**
     * @var OutputInterface
     */
    private $output;

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

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setColumns(ColumnCollectionInterface $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return ColumnCollectionInterface
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    public function execute()
    {
        $columns = $this->columns->getSortedActiveColumns();

        if (!is_array($this->data) && !$this->data instanceof \Traversable) {
            throw new InvalidArgumentException('The supplied data is not traversable.');
        }

        $this->output->begin();

        foreach ($this->data as $idx => $a) {
            $record = $this->processRow($columns, $a);
            $this->output->writeRecord($columns, $record);
        }

        $this->output->end();
        return $this;
    }

    /**
     * @param Column[] $sortedColumns
     * @param $row
     * @return array
     * @throws InvalidArgumentException
     */
    private function processRow(array $sortedColumns, $row)
    {
        $record = array();

        foreach ($sortedColumns as $pos => $column) {
            $options = $column->getOptions();
            $columnType = $column->getType();

            if ($columnType instanceof SimpleExporterTypeInterface) { // assume column with child type are simple type for now
                $rawValue = $this->valueResolver->getValue($row, $column, $options);
                $value = $columnType->getValue($rawValue, $options);
            } elseif ($columnType instanceof ComplexExporterTypeInterface) {
                $value = $columnType->getValue($row, $column->getName(), $options);
            } else {
                throw new InvalidArgumentException('Column type must either implement SimpleExporterTypeInterface or ComplexExporterTypeInterface');
            }

            if ($column->hasChildren()) {
                $record[$column->getName()] = $this->processRow($column->getSortedActiveColumns(), $value);
            } else {
                $record[$column->getName()] = $value;
            }
        }

        return $record;
    }


    public function getResult()
    {
        return $this->output->getResult();
    }

}