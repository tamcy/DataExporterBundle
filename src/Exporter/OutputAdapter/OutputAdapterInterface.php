<?php

namespace Sparkson\DataExporterBundle\Exporter\OutputAdapter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;

/**
 * Interface for exporter output adapter.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
interface OutputAdapterInterface
{
    /**
     * Called when the exporter begins exporting data.
     */
    public function begin();

    /**
     * Called when the exporter exports a record.
     *
     * @param Column[] $columns The column set
     * @param array    $record The record
     */
    public function writeRecord(array $columns, array $record);

    /**
     * Called when the exporter finishes exporting data.
     */
    public function end();

    /**
     * Returns the buffered result produced by this output adapter.
     *
     * @return string|null
     */
    public function getResult();
}