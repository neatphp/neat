<?php
namespace Neat\Base;

use Neat\Config\Config;
use Neat\Event\Event;

/**
 * Component properties can be accessed like public class members and should be injected by DI Container.
 *
 * @property \Neat\Config\Config     config
 * @property \Neat\Event\Dispatcher  dispatcher
 * @property \Neat\Profiler\Profiler profiler
 */
class Component extends Object
{
    /** @var true */
    protected $readonly = true;

    /**
     * Returns value of an option.
     *
     * @param string $option
     *
     * @return Config|string
     */
    public function getConfig($option = null)
    {
        return $option ? $this->config->get($option) : $this->config;
    }

    /**
     * Returns an event.
     *
     * @param string $eventName
     * @param array  $values
     *
     * @return Event
     */
    public function dispatchEvent($eventName, array $values = [])
    {
        return $this->dispatcher->dispatchEvent($eventName, $values, $this);
    }

    /**
     * Adds a debug information.
     *
     * @param mixed $variable
     * @param string $description
     *
     * @return void
     */
    public function debug($variable, $description = null)
    {
        $backtrace = debug_backtrace();
        $item = array_shift($backtrace);
        $this->profiler->debug($variable, $description, $item['file'], $item['line']);
    }
}