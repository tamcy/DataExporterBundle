<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;

use Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A column.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class Column extends AbstractColumnContainer implements ColumnInterface
{
    /**
     * The column name, which is unique in the column set.
     *
     * @var string
     */
    private $name;

    /**
     * The value type of this column.
     *
     * @var ExporterTypeInterface
     */
    private $type;

    /**
     * Column and type options
     *
     * @var array
     */
    private $options;

    /**
     * Whether this column is enabled.
     *
     * @var boolean
     */
    private $enabled = true;

    /**
     * The position of this column in the column set.
     *
     * @var int
     */
    private $position;

    public function __construct($name, ExporterTypeInterface $type, array $options = array())
    {
        $resolver = new OptionsResolver();
        $type->setDefaultOptions($resolver);

        $this->options = $resolver->resolve($options);

        $this->name = $name;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        if ($this->options['label']) {
            return $this->options['label'];
        }

        return ucwords(ltrim(str_replace("_", " ", preg_replace('/([^A-Z])([A-Z])/', "\\1 \\2", $this->getName()))));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->assertNotLocked();
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->assertNotLocked();
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->assertNotLocked();
        $this->position = $position;
    }

}