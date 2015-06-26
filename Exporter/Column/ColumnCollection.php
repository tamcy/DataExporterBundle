<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;

class ColumnCollection implements \ArrayAccess
{
    private $columns;

    public function add(Column $column)
    {
        if (!isset($this->columns[$column->getName()])) {
            $position = count($this->columns);
        } else {
            $position = $column->getPosition();
        }
        $column->setPosition($position);
        $this->columns[$column->getName()] = $column;
    }

    public function get($columnName)
    {
        if (isset($this->columns[$columnName])) {
            return $this->columns[$columnName];
        };

        throw new \Exception('Column name not found!');
    }

    public function has($columnName)
    {
        return isset($this->columns[$columnName]);
    }

    public function remove($columnName)
    {
        if ($this->has($columnName)) {
            unset($this->columns[$columnName]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @return Column[]
     */
    public function getSortedActiveColumns()
    {
        $columns = array_values(array_filter($this->columns, function (Column $col) {
            return $col->isEnabled();
        }));

        usort($columns, function (Column $colA, Column $colB) {
            return $colA->getPosition() - $colB->getPosition();
        });

        return $columns;
    }

}