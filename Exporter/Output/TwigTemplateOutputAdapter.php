<?php

namespace Sparkson\DataExporterBundle\Exporter\Output;

use Sparkson\DataExporterBundle\Exporter\Column\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Twig output adapter.
 *
 * This adapter accepts a `template` option, which defaults to `Resources/view/exporter/template.html`
 * to render the data set in an HTML table. For performance reason the exported data will be stored in
 * a temporary variable, and the template rendering is only called once when end() is called.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class TwigTemplateOutputAdapter extends BaseFlattenOutputAdapter
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var null|array Header data
     */
    protected $header = null;

    /**
     * @var array The data set
     */
    protected $dataSet = array();

    /**
     * @var string The rendered result
     */
    protected $result = null;

    /**
     * @param \Twig_Environment $twig
     * @param array $options
     */
    public function __construct(\Twig_Environment $twig, array $options = array())
    {
        parent::__construct($options);
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'template' => realpath(__DIR__ . '/../../Resources/view/template.html.twig'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        parent::begin();
    }

    /**
     * {@inheritdoc}
     */
    protected function writeHeaderRow(array $columns)
    {
        $this->header = $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function writeRecordRow(array $columns, array $record)
    {
        $fields = array();

        /** @var Column $column */
        foreach ($columns as $key => $column) {
            $fields[] = $record[$key];
        }

        $this->dataSet[] = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        $this->result = $this->twig->render($this->options['template'], array(
            'header' => $this->header,
            'data_set' => $this->dataSet,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->result;
    }

}