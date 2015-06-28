<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter;

use Sparkson\DataExporterBundle\Exporter\Exception\InvalidArgumentException;
use Sparkson\DataExporterBundle\Exporter\ValueResolver\FilterInterface;

class CustomFilter implements FilterInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('The supplied callback is not callable.');
        }

        $this->callback = $callback;
    }

    public function filterValue($value)
    {
        return call_user_func($this->callback, $value);
    }

}