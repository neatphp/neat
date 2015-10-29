<?php
namespace Neat\Event;

use Neat\Data\Structure\PriorityList;

/**
 * Event dispatcher.
 */
class Dispatcher
{
    /** @var PriorityList[] */
    private $listeners;

    /**
     * Adds a listener to an event.
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     *
     * @return Dispatcher
     */
    public function addListener($eventName, callable $listener, $priority = 0)
    {
        if (!isset($this->listeners[$eventName])) $this->listeners[$eventName] = new PriorityList;
        $this->listeners[$eventName]->insert($listener, $priority);

        return $this;
    }

    /**
     * Dispatches an event;
     *
     * @param string $eventName
     * @param array  $values
     * @param mixed  $subject
     *
     * @return Event
     */
    public function dispatchEvent($eventName, array $values, $subject = null)
    {
        $event = new Event($eventName, $subject);
        $event->setValues($values);

        if (!isset($this->listeners[$eventName])) return $event;

        foreach ($this->listeners[$eventName] as $listener) {
            call_user_func($listener, $event);
            if ($event->isCancelled()) break;
        }

        return $event;
    }
}