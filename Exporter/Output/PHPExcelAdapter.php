<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Symfony\Component\OptionsResolver\OptionsResolver;

class PHPExcelAdapter extends BaseFlattenOutputAdapter
{
    /**
     * @var \PHPExcel_Writer_IWriter
     */
    private $writer;

    /**
     * @var \PHPExcel
     */
    private $excel;

    /**
     * @var \PHPExcel_Worksheet
     */
    private $worksheet;

    private $row;

    private $data;

    private $headerDrawn = false;

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'output' => 'php://output',
            'title' => 'Exported data',
            'keep_result' => true,
            'header' => true,
            'writer' => 'Excel5',
        ));

        $resolver->setAllowedTypes('writer', array('string'));
    }

    private function initializeWriter()
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

    private function guessCellType($value)
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

    protected function writeHeaderRow(array $columnLabels)
    {
        foreach ($columnLabels as $idx => $label) {
            $cell = $this->worksheet->getCellByColumnAndRow($idx, $this->row);
            $cell->setValueExplicit($label, \PHPExcel_Cell_DataType::TYPE_STRING);

            $style = $this->worksheet->getStyleByColumnAndRow($idx, $this->row);
            $style->getFont()->setBold(true);
        }

        $this->row++;

        $this->headerDrawn = true;
    }

    protected function writeRecordRow(array $columnLabels, array $record)
    {
        $col = 0;

        foreach ($columnLabels as $idx => $label) {
            $value = $record[$idx];

            if ((string)$value !== '') {
                $cell = $this->worksheet->getCellByColumnAndRow($col, $this->row);
                $this->worksheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                $cell->setValueExplicit($value, $this->guessCellType($value));
            }

            $col++;
        }

        $this->row++;
    }

    public function end()
    {
        $this->initializeWriter();
        $this->writer->save($this->options['output']);

        if ($this->options['keep_result'] && is_file($this->options['output'])) {
            $this->data = file_get_contents($this->options['output']);
        }
    }

    public function getResult()
    {
        return $this->data;
    }

}