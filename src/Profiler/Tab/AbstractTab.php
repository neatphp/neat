<?php
namespace Neat\Profiler\Tab;

/**
 * Abstract tab.
 */
abstract class AbstractTab
{
    /**
     * Retrieves the ID.
     *
     * @return string
     */
    public function getId()
    {
        return str_replace('\\', '-', get_class($this));
    }

    /**
     * Retrieves the name.
     *
     * @return string
     */
    public function getName()
    {
        $class = explode('\\', get_class($this));
        return array_pop($class);
    }

    /**
     * Retrieves the content.
     *
     * @return string
     */
    abstract public function getContent();
}