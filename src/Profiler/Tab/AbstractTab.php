<?php
namespace Neat\Profiler\Tab;

/**
 * Abstract tab.
 */
abstract class AbstractTab
{
    /**
     * Returns the ID.
     *
     * @return string
     */
    public function getId()
    {
        return str_replace('\\', '-', get_class($this));
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        $class = explode('\\', get_class($this));
        return array_pop($class);
    }

    /**
     * Returns the content.
     *
     * @return string
     */
    abstract public function getContent();
}