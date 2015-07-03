<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

/**
 * Interface for exporter output.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
interface OutputInterface
{
    /**
     * Called when the exporter begins exporting data.
     */
    public function begin();

    /**
     * Called when the exporter exports a record.
     *
     * @param array $columns The column set
     * @param array $record The record
     */
    public function writeRecord(array $columns, array $record);

    /**
     * Called when the exporter finishes exporting data.
     */
    public function end();

    /**
     * Returns the result produced by this output.
     *
     * @return string
     */
    public function getResult();
}