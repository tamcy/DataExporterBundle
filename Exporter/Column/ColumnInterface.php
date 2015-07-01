<?php

namespace Sparkson\DataExporterBundle\Exporter\Column;

use Sparkson\DataExporterBundle\Exporter\Type\ExporterTypeInterface;

interface ColumnInterface
{
    public function setEnabled($enabled);

    public function setPosition($position);

    public function setOptions($options);

    public function getLabel();

    public function getName();

    /**
     * @return ExporterTypeInterface
     */
    public function getType();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @return boolean
     */
    public function isEnabled();

    /**
     * @return int
     */
    public function getPosition();


}