<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\AbstractOutputAdapter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PHPExcelAdapter extends AbstractOutputAdapter
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
    private $col;

    private $data;

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
        $this->data = null;
        $this->row = 1;

        $this->excel = new \PHPExcel;
        $props = $this->excel->getProperties();
        $props->setTitle($this->options['title']);
        $this->excel->setActiveSheetIndex(0);

        $this->worksheet = $this->excel->getActiveSheet();

        if ($this->options['header']) {

            foreach ($this->columns as $idx => $value) {
                $cell = $this->worksheet->getCellByColumnAndRow($idx, $this->row);
                $cell->setValueExplicit($value, \PHPExcel_Cell_DataType::TYPE_STRING);

                $style = $this->worksheet->getStyleByColumnAndRow($idx, $this->row);
                $style->getFont()->setBold(true);
            }

            $this->row++;
        }
    }

    public function beginRow()
    {
        $this->col = 0;
    }

    public function writeColumn($value, array $options)
    {
        if ((string)$value !== '') {
            $cell = $this->worksheet->getCellByColumnAndRow($this->col, $this->row);
            $this->worksheet->getColumnDimensionByColumn($this->col)->setAutoSize(true);
            $cell->setValueExplicit($value, $this->guessCellType($value));
        }
        $this->col++;
    }

    public function endRow()
    {
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