<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\AbstractOutputAdapter;
use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CSVAdapter extends AbstractOutputAdapter
{
    private $handle;

    private $data;

    private $headerDrawn = false;

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'output' => 'php://temp/maxmemory:5242880',
            'keep_result' => true,
            'header' => true,
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_char' => '\\',
        ));

        $resolver->setAllowedTypes('keep_result', 'bool');
    }

    public function begin()
    {
        $this->handle = fopen($this->options['output'], 'r+');
        $this->data = null;
    }

    protected function drawHeader(array $columns)
    {
        $columnLabels = array_map(function (Column $column) {
            return $column->getLabel();
        }, $columns);

        if (!$this->headerDrawn && $this->options['header']) {
            fputcsv($this->handle, $columnLabels, $this->options['delimiter'], $this->options['enclosure'], $this->options['escape_char']);
        }

        $this->headerDrawn = true;
    }

    public function writeRecord(array $columns, array $record)
    {
        $this->drawHeader($columns);

        $fields = array();

        /** @var Column $column */
        foreach ($columns as $column) {
            $fields[] = $record[$column->getName()];
        }

        fputcsv($this->handle, $fields, $this->options['delimiter'], $this->options['enclosure'], $this->options['escape_char']);
    }

    public function end()
    {
        if ($this->options['keep_result']) {
            rewind($this->handle);
            $this->data = stream_get_contents($this->handle);
        }

        fclose($this->handle);
    }

    public function getResult()
    {
        return $this->data;
    }

}