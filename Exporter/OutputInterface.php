<?php

namespace Sparkson\DataExporterBundle\Exporter;


interface OutputInterface
{
    public function setColumns(array $columns);

    public function begin();

    public function beginRow();

    public function writeColumn($value, array $options);

    public function endRow();

    public function end();

    public function getResult();
}