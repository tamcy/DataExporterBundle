<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\AbstractOutputAdapter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CSVAdapter extends AbstractOutputAdapter
{
    private $handle;

    private $fields;

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
        $this->handle = fopen($this->options['output'], 'r+');
        $this->data = null;
        if ($this->options['header']) {
            fputcsv($this->handle, $this->columns, $this->options['delimiter'], $this->options['enclosure'], $this->options['escape_char']);
        }
    }

    public function beginRow()
    {
        $this->fields = array();
    }

    public function writeColumn($value, array $options)
    {
        $this->fields[] = $value;
    }

    public function endRow()
    {
        fputcsv($this->handle, $this->fields, $this->options['delimiter'], $this->options['enclosure'], $this->options['escape_char']);
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