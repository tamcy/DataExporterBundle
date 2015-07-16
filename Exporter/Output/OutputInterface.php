<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;
use Sparkson\DataExporterBundle\Exporter\Column\Column;

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
     * @param Column[] $columns The column set
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