<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CSV output adapter.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class CSVAdapter extends BaseFlattenOutputAdapter
{
    protected $handle;

    protected $data;

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'filename' => 'php://temp/maxmemory:5242880',
            'keep_result' => true,
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_char' => '\\',
        ));

        $resolver->setAllowedTypes('keep_result', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        parent::begin();
        $this->handle = fopen($this->options['filename'], 'w+');
        $this->data = null;
    }

    /**
     * {@inheritdoc}
     */
    protected function writeHeaderRow(array $columns)
    {
        $labels = array();
        /**
         * @var string $key
         * @var Column $column
         */
        foreach ($columns as $key => $column) {
            $labels[$key] = $column->getLabel();
        }

        fputcsv($this->handle, $labels, $this->options['delimiter'], $this->options['enclosure'], $this->options['escape_char']);
    }

    /**
     * {@inheritdoc}
     */
    protected function writeRecordRow(array $columns, array $record)
    {
        $fields = array();

        /** @var Column $column */
        foreach ($columns as $key => $columnLabel) {
            $fields[] = $record[$key];
        }

        fputcsv($this->handle, $fields, $this->options['delimiter'], $this->options['enclosure'], $this->options['escape_char']);
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        if ($this->options['keep_result']) {
            rewind($this->handle);
            $this->data = stream_get_contents($this->handle);
        }

        fclose($this->handle);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->data;
    }

}