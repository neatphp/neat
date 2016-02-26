<?php
namespace Neat\Base;

use Neat\Config\Config;
use Neat\Event\Event;

/**
 * Component properties can be accessed like public class members and should be injected by DI Container.
 *
 * @property-read \Neat\Config\Config     config
 * @property-read \Neat\Event\Dispatcher  dispatcher
 * @property-read \Neat\Profiler\Profiler profiler
 */
class Component extends Object
{
    /**
     * Retrieves value of an option.
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
     * Dispatches an event.
     *
     * @param string $eventName
     * @param array  $args
     *
     * @return Event
     */
    public function dispatchEvent($eventName, array $args = [])
    {
        return $this->dispatcher->dispatchEvent($eventName, $this, $args);
    }
}