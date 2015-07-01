<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;

interface ColumnCollectionInterface extends \ArrayAccess, \Countable
{
    public function setChildren(array $children);

    public function hasChildren();

    public function getChildren();

    public function addChild(ColumnInterface $column);

    public function getChild($columnName);

    public function hasChild($columnName);

    public function removeChild($columnName);

    public function build();

    /**
     * @return Column[]
     */
    public function getBuiltColumns();

}