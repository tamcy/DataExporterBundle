<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;


abstract class AbstractColumnContainer implements ColumnCollectionInterface
{
    protected $children;

    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return $this->children && count($this->children) > 0;
    }

    public function addChild(ColumnInterface $column)
    {
        if (!isset($this->children[$column->getName()])) {
            $position = count($this->children);
        } else {
            $position = $column->getPosition();
        }
        $column->setPosition($position);
        $this->children[$column->getName()] = $column;
    }

    public function getChild($columnName)
    {
        if (isset($this->children[$columnName])) {
            return $this->children[$columnName];
        };

        throw new \Exception('Column name not found!');
    }

    public function hasChild($columnName)
    {
        return isset($this->children[$columnName]);
    }

    public function removeChild($columnName)
    {
        if ($this->hasChild($columnName)) {
            unset($this->children[$columnName]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->hasChild($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->getChild($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->addChild($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->removeChild($offset);
    }

    /**
     * @return Column[]
     */
    public function getSortedActiveColumns()
    {
        $columns = array_values(array_filter($this->children, function (ColumnInterface $col) {
            return $col->isEnabled();
        }));

        usort($columns, function (ColumnInterface $colA, ColumnInterface $colB) {
            return $colA->getPosition() - $colB->getPosition();
        });

        return $columns;
    }

    public function count()
    {
        return count($this->children);
    }


}