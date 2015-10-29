<?php
namespace Neat\Event;

use Neat\Data\Data;

/**
 * Event.
 */
class Event extends Data
{
    /** @var string */
    private $name;

    /** @var mixed */
    private $subject;

    /** @var bool  */
    private $cancelled = false;

    /**
     * Constructor.
     *
     * @param string $name
     * @param mixed $subject
     */
    public function __construct($name, $subject)
    {
        parent::__construct(true, false);

        $this->name = $name;
        $this->subject = $subject;
    }

    /**
     * Returns the event name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the subject
     *
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
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
     * Sets the flag, which tells whether event has been cancelled.
     *
     * @param bool $flag
     *
     * @return Event
     */
    public function setCancel($flag)
    {
        $this->cancelled = $flag;
        return $this;
    }
}