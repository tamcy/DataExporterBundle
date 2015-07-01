<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;


use Sparkson\DataExporterBundle\Exporter\Exception\InvalidOperationException;

abstract class AbstractColumnContainer implements ColumnCollectionInterface
{
    protected $children;

    /**
     * @var Column[]
     */
    protected $sortedColumns;

    protected $locked = false;

    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    protected function assertNotLocked()
    {
        if ($this->locked) {
            throw new InvalidOperationException("Cannot modify a locked column set");
        }
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
        $this->assertNotLocked();

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

    public function build()
    {
        if ($this->hasChildren()) {
            $columns = array_values(array_filter($this->children, function (ColumnInterface $col) {
                return $col->isEnabled();
            }));

            usort($columns, function (ColumnInterface $colA, ColumnInterface $colB) {
                return $colA->getPosition() - $colB->getPosition();
            });

            foreach ($columns as $column) {
                $column->build();
            }
            $this->sortedColumns = $columns;
        }
        $this->locked = true;
    }

    /**
     * @return Column[]
     */
    public function getBuiltColumns()
    {
        return $this->sortedColumns;
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

    public function count()
    {
        return count($this->children);
    }


}