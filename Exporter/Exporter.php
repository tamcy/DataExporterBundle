<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnCollectionInterface;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Exception\InvalidArgumentException;
use Sparkson\DataExporterBundle\Exporter\Output\OutputInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ColumnValueResolverInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\SimpleTypeColumnValueResolver;

/**
 * The Exporter class.
 *
 * Normally this class is not created manually but built via ExporterBuilder::getExporter().
 */
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
        $this->columns = new ColumnSet();
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
        $this->columns->build();

        $columns = $this->columns->getBuiltColumns();

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

            $value = $columnType->getValue($this->valueResolver, $row, $column->getName(), $options);

            if ($column->hasChildren()) {
                $record[$column->getName()] = $this->processRow($column->getBuiltColumns(), $value);
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