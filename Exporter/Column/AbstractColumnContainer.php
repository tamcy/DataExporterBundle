<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;


use Sparkson\DataExporterBundle\Exporter\Exception\InvalidOperationException;

/**
 * The base column set class.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
abstract class AbstractColumnContainer implements ColumnCollectionInterface
{
    /**
     * @var Column[] Array of columns
     */
    protected $children;

    /**
     * @var Column[] Array of built columns
     */
    protected $sortedColumns;

    /**
     * @var bool True if the column set is built for export and locked
     */
    protected $locked = false;

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return $this->children && count($this->children) > 0;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getChild($columnName)
    {
        if (isset($this->children[$columnName])) {
            return $this->children[$columnName];
        };

        throw new \Exception('Column name not found!');
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild($columnName)
    {
        return isset($this->children[$columnName]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild($columnName)
    {
        if ($this->hasChild($columnName)) {
            unset($this->children[$columnName]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setColumnOrders(array $columnNames)
    {
        foreach ($columnNames as $position => $columnName) {
            $this->getChild($columnName)->setPosition($position + 1);
        }
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->children);
    }

}