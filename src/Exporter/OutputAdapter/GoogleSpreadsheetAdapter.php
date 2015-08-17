<?php

namespace Sparkson\DataExporterBundle\Exporter\OutputAdapter;

use Google\Spreadsheet\Worksheet;
use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Google spreadsheet output adapter.
 *
 * This adapter writes the exported result to a Google Spreadsheet worksheet.
 * Requies "asimlqt/php-google-spreadsheet-client" package.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class GoogleSpreadsheetAdapter extends BaseFlattenAdapter
{
    /**
     * @var \Google\Spreadsheet\Worksheet
     */
    protected $worksheet;

    /**
     * @var \Google\Spreadsheet\ListFeed
     */
    protected $listFeed;

    /**
     * @var \Google\Spreadsheet\CellFeed
     */
    protected $cellFeed;

    /**
     * @var array
     */
    protected $headerKeys = array();

    /**
     * @param Worksheet $worksheet
     * @param array     $options
     */
    public function __construct(Worksheet $worksheet, array $options = array())
    {
        parent::__construct($options);
        $this->worksheet = $worksheet;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        parent::begin();
        $this->listFeed = $this->worksheet->getListFeed();
        $this->cellFeed = $this->worksheet->getCellFeed();
    }

    /**
     * {@inheritdoc}
     */
    protected function writeHeaderRow(array $columns)
    {
        $cellFeed = $this->worksheet->getCellFeed();
        $col = 0;

        /**
         * @var string $key
         * @var Column $column
         */
        foreach ($columns as $key => $column) {
            $cellFeed->editCell(1, ++$col, $column->getLabel());
            $this->headerKeys[$key] = preg_replace('/[^0-9a-z]/', '', strtolower($column->getLabel()));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function writeRecordRow(array $columns, array $record)
    {
        $row = array();

        /** @var Column $column */
        foreach ($columns as $key => $columnLabel) {
            $row[$this->headerKeys[$key]] = $record[$key];
        }
        $this->listFeed->insert($row);
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return null;
    }


}