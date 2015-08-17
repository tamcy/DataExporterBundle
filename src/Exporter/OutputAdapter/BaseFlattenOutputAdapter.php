<?php

namespace Sparkson\DataExporterBundle\Exporter\OutputAdapter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract flattened output adapter.
 *
 * The output column (and the exported record) can be nested. To simplify the
 * job of output adapters, this class flattens the nested properties into
 * a two dimensional array. Adapters extending from this class only needs to
 * implement the writeHeaderRow() and writeRecordRow() methods.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
abstract class BaseFlattenOutputAdapter extends AbstractOutputAdapter
{
    private $waitForFirstRecord = true;

    private $flatColumns = array();

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        $this->waitForFirstRecord = true;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'header' => true,
        ));
    }

    protected function getColumnName(Column $column, array $prefixes)
    {
        if (!$prefixes) {
            return $column->getName();
        }

        return implode('.', $prefixes) . '.' . $column->getName();
    }

    protected function flattenColumns(array $columns, array $prefixes = [])
    {
        /** @var Column $column */
        foreach ($columns as $column) {
            if ($column->hasChildren()) {
                $prefixes[] = $column->getName();
                $this->flattenColumns($column->getBuiltColumns(), $prefixes);
            } else {
                $this->flatColumns[$this->getColumnName($column, $prefixes)] = $column;
            }
        }
    }

    protected function initializeHeader(array $columns)
    {
        $this->flattenColumns($columns);

        if ($this->options['header']) {
            $this->writeHeaderRow($this->flatColumns);
        }
    }

    protected function flattenRecord(&$result, $record, array $columns, array $prefixes = [])
    {
        /** @var Column $column */
        foreach ($columns as $column) {
            if ($column->hasChildren()) {
                $prefixes[] = $column->getName();
                $this->flattenRecord($result, $record[$column->getName()], $column->getBuiltColumns(), $prefixes);
            } else {
                $key = $this->getColumnName($column, $prefixes);
                $result[$key] = $record[$column->getName()];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeRecord(array $columns, array $record)
    {
        if ($this->waitForFirstRecord) {
            $this->initializeHeader($columns);
            $this->waitForFirstRecord = false;
        }

        $this->flattenRecord($result, $record, $columns);
        $this->writeRecordRow($this->flatColumns, $result);
    }

    /**
     * Writes the header row.
     *
     * This method will be called before the first record is written when `header` is
     * set to true in options.
     *
     * @param Column[] $columns
     */
    abstract protected function writeHeaderRow(array $columns);

    /**
     * Writes the record row.
     *
     * This method will be called on each record.
     * $columnLabels is a sorted associative array with key equals to a unique column name and
     * value equals to header label.
     * $record is an associative array with key equals to a unique column name.
     *
     * @param Column[] $columns The column
     * @param array    $record The record
     */
    abstract protected function writeRecordRow(array $columns, array $record);

}