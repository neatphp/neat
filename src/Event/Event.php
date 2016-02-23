<?php
namespace Neat\Event;

use Neat\Data\Data;

/**
 * Event.
 */
class Event
{
    /** @var string */
    private $name;

    /** @var mixed */
    private $subject;

    /** @var Data */
    private $params;

    /** @var bool  */
    private $cancelled = false;

    /**
     * Constructor.
     *
     * @param string $name
     * @param mixed  $subject
     * @param array  $args
     */
    public function __construct($name, $subject, array $args)
    {
        $this->name    = $name;
        $this->subject = $subject;
        $this->params  = new Data(array_keys($args));
        $this->params->load($args);
    }

    /**
     * Retrieves the event name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieves the subject.
     *
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Retrieves value of a parameter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParam($name)
    {
        return $this->params[$name];
    }

    /**
     * Tells whether event has been cancelled.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * Sets value of a parameter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Sets the flag, which tells whether event has been cancelled.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function setCancel($flag)
    {
        $this->cancelled = $flag;

        return $this;
    }
}