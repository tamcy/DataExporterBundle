<?php

namespace Sparkson\DataExporterBundle\Exporter\OutputAdapter;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PHPExcel output adapter.
 *
 * This adapter writes the export result to a format supported by the
 * PHPExcel library.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class PHPExcelAdapter extends BaseFlattenOutputAdapter
{
    /**
     * @var \PHPExcel_Writer_IWriter
     */
    protected $writer;

    /**
     * @var \PHPExcel
     */
    protected $excel;

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $worksheet;

    protected $row;

    protected $data;

    protected $headerDrawn = false;

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'filename' => null,
            'title' => 'Exported data',
            'keep_result' => true,
            'header' => true,
            'writer' => 'Excel5',
        ));

        $resolver->setAllowedTypes('writer', array('string'));
    }

    protected function initializeWriter()
    {
        $cls = $this->options['writer'];
        if (!class_exists($cls)) {
            $cls = 'PHPExcel_Writer_' . $cls;
        }

        if (!class_exists($cls)) {
            throw new \Exception(sprintf('Unable to load class %s or PHPExcel_Writer_%s'), $this->options['writer'], $this->options['writer']);
        }

        $this->writer = new $cls($this->excel);

        if (!$this->writer instanceof \PHPExcel_Writer_IWriter) {
            throw new \Exception('invalid writer class!');
        }
    }

    protected function guessCellType($value)
    {
        if (is_null($value)) {
            return \PHPExcel_Cell_DataType::TYPE_NULL;
        } elseif ($value === '') {
            return \PHPExcel_Cell_DataType::TYPE_STRING;
        } elseif ($value instanceof \PHPExcel_RichText) {
            return \PHPExcel_Cell_DataType::TYPE_STRING;
        } elseif (is_numeric($value)) {
            return \PHPExcel_Cell_DataType::TYPE_NUMERIC;
        } else {
            return \PHPExcel_Cell_DataType::TYPE_STRING;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        parent::begin();

        $this->data = null;
        $this->row = 1;

        $this->excel = new \PHPExcel;
        $props = $this->excel->getProperties();
        $props->setTitle($this->options['title']);
        $this->excel->setActiveSheetIndex(0);

        $this->worksheet = $this->excel->getActiveSheet();
    }

    /**
     * {@inheritdoc}
     */
    protected function writeHeaderRow(array $columns)
    {
        $idx = 0;
        /**
         * @var string $key
         * @var Column $column
         */
        foreach ($columns as $key => $column) {
            $cell = $this->worksheet->getCellByColumnAndRow($idx, $this->row);
            $cell->setValueExplicit($column->getLabel(), \PHPExcel_Cell_DataType::TYPE_STRING);

            $style = $this->worksheet->getStyleByColumnAndRow($idx, $this->row);
            $style->getFont()->setBold(true);

            $options = $column->getOptions();
            $col = $this->worksheet->getColumnDimensionByColumn($idx);
            if (!empty($options['output_options']['width'])) {
                $col->setAutoSize(false)->setWidth($options['output_options']['width']);
            } else {
                $col->setAutoSize(true);
            }
            $idx++;
        }

        $this->row++;

        $this->headerDrawn = true;
    }

    /**
     * {@inheritdoc}
     */
    protected function writeRecordRow(array $columns, array $record)
    {
        $col = 0;

        /**
         * @var string $key
         * @var Column $column
         */
        foreach ($columns as $key => $column) {
            $value = $record[$key];

            if ((string)$value !== '') {
                $cell = $this->worksheet->getCellByColumnAndRow($col, $this->row);
                $cell->setValueExplicit($value, $this->guessCellType($value));
            }

            $options = $column->getOptions();
            if (!empty($options['output_options']['wrap_text'])) {
                $style = $this->worksheet->getStyleByColumnAndRow($col, $this->row);
                $style->getAlignment()->setWrapText(true);
            }

            $col++;
        }

        $this->row++;
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        $this->initializeWriter();

        if (null === $this->options['filename']) {
            $file = tempnam(sys_get_temp_dir(), 'phpexcel_export');
            $isTempFile = true;
        } else {
            $file = $this->options['filename'];
            $isTempFile = false;
        }

        $this->writer->save($file);
        if ($this->options['keep_result'] && is_file($file)) {
            $this->data = file_get_contents($file);
        }

        if ($isTempFile) {
            @unlink($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->data;
    }

}