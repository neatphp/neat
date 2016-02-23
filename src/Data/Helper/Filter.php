<?php
namespace Neat\Data\Helper;

/**
 * Filter.
 */
class Filter
{
    /** @var callable[] */
    private $callbacks = [];

    /**
     * Filtrates a value.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function filtrate($name, $value)
    {
        if (!isset($this->callbacks[$name])) {
            return $value;
        }

        foreach ($this->callbacks[$name] as $callback) {
            $value = $callback($value);
        }

        return $value;
    }

    /**
     * Appends a callback.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return self
     */
    public function append($name, callable $callback)
    {
        if (!isset($this->callbacks[$name])) {
            $this->callbacks[$name] = [];
        }

        $this->callbacks[$name][] = $callback;

        return $this;
    }

    /**
     * Removes callbacks.
     *
     * @param string $name
     *
     * @return self
     */
    public function remove($name)
    {
        unset($this->callbacks[$name]);

        return $this;
    }
}