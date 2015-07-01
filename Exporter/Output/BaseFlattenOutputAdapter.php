<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\Column\Column;

abstract class BaseFlattenOutputAdapter extends AbstractOutputAdapter
{
    private $waitForFirstRecord = true;

    private $flatColumnLabels = array();

    public function begin()
    {
        $this->waitForFirstRecord = true;
    }

    private function getColumnName(Column $column, array $prefixes)
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
                $this->flatColumnLabels[$this->getColumnName($column, $prefixes)] = $column->getLabel();
            }
        }
    }

    protected function initializeHeader(array $columns)
    {
        $this->flattenColumns($columns);

        if ($this->options['header']) {
            $this->writeHeaderRow($this->flatColumnLabels);
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

    public function writeRecord(array $columns, array $record)
    {
        if ($this->waitForFirstRecord) {
            $this->initializeHeader($columns);
            $this->waitForFirstRecord = false;
        }

        $this->flattenRecord($result, $record, $columns);
        $this->writeRecordRow($this->flatColumnLabels, $result);
    }

    abstract protected function writeHeaderRow(array $columnLabels);

    abstract protected function writeRecordRow(array $columnLabels, array $record);

}