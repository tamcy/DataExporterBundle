<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CSVAdapter extends BaseFlattenOutputAdapter
{
    private $handle;

    private $data;

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
        parent::begin();
        $this->handle = fopen($this->options['output'], 'r+');
        $this->data = null;
    }

    protected function writeHeaderRow(array $columnLabels)
    {
        fputcsv($this->handle, $columnLabels, $this->options['delimiter'], $this->options['enclosure'], $this->options['escape_char']);
    }

    protected function writeRecordRow(array $columnLabels, array $record)
    {
        $fields = array();

        /** @var Column $column */
        foreach ($columnLabels as $key => $columnLabel) {
            $fields[] = $record[$key];
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