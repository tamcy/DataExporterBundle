<?php

namespace Sparkson\DataExporterBundle\Exporter\ValueResolver\Filter;

use Sparkson\DataExporterBundle\Exporter\Exception\InvalidArgumentException;

/**
 * A custom filter.
 *
 * User can pass a callback to the constructor which will be called when a value is
 * going to be filtered.
 */
class CustomFilter implements FilterInterface
{
    /**
     * @var callable The callback
     */
    private $callback;

    /**
     * Class constructor.
     *
     * @param $callback
     * @throws InvalidArgumentException When the supplied callback is not callable.
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('The supplied callback is not callable.');
        }

        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function filterValue($value)
    {
        return call_user_func($this->callback, $value);
    }

}