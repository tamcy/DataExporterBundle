<?php

namespace Sparkson\DataExporterBundle\Exporter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnCollectionInterface;
use Sparkson\DataExporterBundle\Exporter\Column\ColumnSet;
use Sparkson\DataExporterBundle\Exporter\Exception\InvalidArgumentException;
use Sparkson\DataExporterBundle\Exporter\OutputAdapter\AdapterInterface;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\DefaultValueResolver;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\ValueResolverInterface;

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
     * @var AdapterInterface
     */
    private $outputAdapter;

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
     * @param AdapterInterface $outputAdapter
     * @return $this
     */
    public function setOutputAdapter(AdapterInterface $outputAdapter)
    {
        $this->outputAdapter = $outputAdapter;

        return $this;
    }

    /**
     * @return AdapterInterface
     */
    public function getOutputAdapter()
    {
        return $this->outputAdapter;
    }

    /**
     * @return ValueResolverInterface
     */
    public function getValueResolver()
    {
        return $this->valueResolver;
    }

    /**
     * @param ValueResolverInterface $valueResolver
     */
    public function setValueResolver($valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    /**
     * Runs the export process.
     *
     * The exporter will iterate over the data set one by one, then iterate over the
     * column set of each record. The output adapter is responsible for writing the
     * exported data in a specific format. The export result can be retrieved with
     * getResult() if the output adapter is configured to write to a buffer.
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

        $this->outputAdapter->begin();

        foreach ($this->dataSet as $idx => $a) {
            $record = $this->processRow($columns, $a);
            $this->outputAdapter->writeRecord($columns, $record);
        }

        $this->outputAdapter->end();

        return $this;
    }

    /**
     * @param Column[] $sortedColumns
     * @param          $row
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
     * Note that an output adapter may not necessary keep a buffered result.
     * For example, it is possible to configure an output adapter to write the
     * exported data to a file (or a remote location) and forget about it.
     * In this case, getResult() will return null.
     *
     * @return string|null
     */
    public function getResult()
    {
        return $this->outputAdapter->getResult();
    }

}