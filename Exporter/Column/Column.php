<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;

use Sparkson\DataExporterBundle\Exporter\Type\ComplexExporterTypeInterface;
use Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface;
use Sparkson\DataExporterBundle\Exporter\Type\SimpleExporterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Column extends AbstractColumnContainer implements ColumnInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ExporterTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var boolean
     */
    private $enabled = true;

    /**
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getLabel()
    {
        if ($this->options['label']) {
            return $this->options['label'];
        }

        return ucwords(ltrim(str_replace("_", " ", preg_replace('/([^A-Z])([A-Z])/', "\\1 \\2", $this->getName()))));
    }

    /**
     * @return ExporterTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

}