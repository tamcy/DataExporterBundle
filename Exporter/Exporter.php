<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnCollectionInterface;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Exception\InvalidArgumentException;
use Sparkson\DataExporterBundle\Exporter\Output\OutputInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ValueResolverInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\DefaultValueResolver;

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
    private $dataSet;

    /**
     * @var ValueResolverInterface
     */
    private $valueResolver;

    /**
     * Class constructor.
     *
     * @param ValueResolverInterface $valueResolver
     */
    public function __construct(ValueResolverInterface $valueResolver = null)
    {
        $this->columns = new ColumnSet();
        $this->valueResolver = $valueResolver ?: new DefaultValueResolver();
    }

    /**
     * Assigns a data set to this exporter.
     *
     * The data set should be an array of a traversable instance (iterable with foreach()),
     * with each element having a structure accessible by the column value resolver.
     * Normally this would be an array of objects.
     *
     * @param mixed $dataSet
     * @return $this
     */
    public function setDataSet($dataSet)
    {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * Sets the column set.
     *
     * @param ColumnCollectionInterface $columns
     * @return $this
     */
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
     * @param OutputInterface $output
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Runs the export process.
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function execute()
    {
        $this->columns->build();

        $columns = $this->columns->getBuiltColumns();

        if (!is_array($this->dataSet) && !$this->dataSet instanceof \Traversable) {
            throw new InvalidArgumentException('The supplied data is not traversable.');
        }

        $this->output->begin();

        foreach ($this->dataSet as $idx => $a) {
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

    /**
     * Returns the exported result.
     *
     * This just proxies to the output adapter's getResult() method.
     *
     * @return string
     */
    public function getResult()
    {
        return $this->output->getResult();
    }

}