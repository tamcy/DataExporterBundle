<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;

use Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface;

/**
 * Interface for a column.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
interface ColumnInterface
{
    /**
     * Enables or disables a column.
     *
     * @param bool $enabled True when enabled, other false
     */
    public function setEnabled($enabled);

    /**
     * Sets the column position.
     *
     * Upon export, the columns will be sorted in ascending order. Two columns with the same order value is allowed,
     * but the resulting order will be undefined.
     *
     * @param int $position The position
     */
    public function setPosition($position);

    /**
     * Sets the options for the exporting column.
     *
     * @param array $options Column options
     */
    public function setOptions($options);

    /**
     * Returns the label (caption/header) of the column.
     *
     * The label will be read from the option's "label" key. If not assigned, it will be generated from the column name.
     *
     * @return string The column label
     */
    public function getLabel();

    /**
     * Returns the name of the column.
     *
     * The column name is the unique key in a column set.
     *
     * @return string The column name
     */
    public function getName();

    /**
     * Returns the value type of this column.
     *
     * @return ExporterTypeInterface
     */
    public function getType();

    /**
     * Returns the column options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns the state of this column.
     *
     * @return boolean
     */
    public function isEnabled();

    /**
     * Returns the assigned position of this column.
     *
     * @return int
     */
    public function getPosition();


}