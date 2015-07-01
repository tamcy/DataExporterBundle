<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;


interface OutputInterface
{
    public function begin();

    public function writeRecord(array $columns, array $record);

    public function end();

    public function getResult();
}