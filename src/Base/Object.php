<?php
namespace Neat\Base;

use Neat\Data\Data;
use ReflectionClass;

/**
 * Base object.
 */
class Object
{
    /** @var Data */
    private $properties;

    /**
     * Deep cloning.
     */
    public function __clone()
    {
        if ($this->properties) {
            $this->properties = clone $this->properties;
        }
    }

    /**
     * Retrieves value of a property.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }

    /**
     * Sets value of a property.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setProperty($name, $value);
    }

    /**
     * Retrieves value of a property.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty($name)
    {
        return $this->getProperties()[$name];
    }

    /**
     * Sets value of a property.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self
     */
    public function setProperty($name, $value)
    {
        $this->getProperties()[$name] = $value;

        return $this;
    }

    /**
     * Initiates properties.
     *
     * @return Data
     */
    public function getProperties()
    {
        if (!$this->properties) {
            $this->properties = new Data;

            $classes[] = new ReflectionClass(get_class($this));
            while ($parentClass = reset($classes)->getParentClass()) {
                array_unshift($classes, $parentClass);
            }

            /** @var ReflectionClass $class */
            foreach ($classes as $class) {
                $comment = $class->getDocComment();
                if ($comment) {
                    $this->parseComment($comment);
                }
            }
        }

        return $this->properties;
    }

    /**
     * Parses comment.
     *
     * @param string $comment
     *
     * @return void
     */
    private function parseComment($comment)
    {
        $lines = explode(PHP_EOL, $comment);
        foreach ($lines as $line) {
            $pos = stripos($line, '@property-read');
            if ($pos !== false) {
                $isReadonly = true;
            } else {
                $pos = stripos($line, '@property');
                $isReadonly = false;
            }

            if (false !== $pos) {
                $property = explode(' ', preg_replace('/[ ]+/', ' ', substr($line, $pos)));

                $name = $property[1];
                if (3 == count($property)) {
                    $name = $property[2];
                    $type = $property[1];
                }

                if ('$' == $name[0]) {
                    $name = substr($name, 1);
                }

                $this->properties->init($name, null, $isReadonly);

                if (isset($type)) {
                    $this->properties->getValidator()->append($name, $type);
                }
            }
        }
    }
}