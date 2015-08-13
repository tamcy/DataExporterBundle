<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;

/**
 * Interface for a column set.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
interface ColumnCollectionInterface extends \ArrayAccess, \Countable
{
    /**
     * Replaces the containing columns with the supplied one.
     *
     * @param array $children The array of columns to replace
     */
    public function setChildren(array $children);

    /**
     * Returns whether this column set has at least one child.
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Returns the children of this column set.
     *
     * @return array
     */
    public function getChildren();

    /**
     * Adds a column to the end of the column set.
     *
     * Column name must be unique in the column set, otherwise existing column
     * with the same column name will be overwritten.
     *
     * @param ColumnInterface $column The column to add
     */
    public function addChild(ColumnInterface $column);

    /**
     * Retrieves the child column by its name.
     *
     * @param string $columnName Name of the column
     * @return Column
     */
    public function getChild($columnName);

    /**
     * Returns whether a column with the supplied column name exists.
     *
     * @param string $columnName Name of the column
     * @return bool True if the column is found, other false
     */
    public function hasChild($columnName);

    /**
     * Removes a column from the column set.
     *
     * @param string $columnName Name of the column
     */
    public function removeChild($columnName);

    /**
     * Builds the column set.
     *
     * This method will build the column set array ready for export use. The resulting column set array will contain
     * only enabled columns, sorted by their assigned positions. The column set will be read-only after this method is
     * run.
     */
    public function build();

    /**
     * Returns the column set built by the build() method.
     *
     * @see build
     * @return Column[] An array of columns ready for export use
     */
    public function getBuiltColumns();

    /**
     * Assigns the position of each column according to the order of the supplied array.
     *
     * This is a helper method so that you don't need to write a bunch of
     * $columnSet->getChild('columnName')->setPosition(...).
     * When $disableOtherColumns is true, columns not specified in $columnNames will be disabled, so that they won't
     * show up in the exported document.
     * Note: columns specified in $columnNames will NOT be enabled explicitly, even when $disableOtherColumns is true.
     *
     * @param array $columnNames
     * @param bool  $disableOtherColumns
     */
    public function setColumnOrders(array $columnNames, $disableOtherColumns = false);


}