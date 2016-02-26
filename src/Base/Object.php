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
     * Retrieves value of a property or path.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getProperties()[$name];
    }

    /**
     * Sets value of a property or path.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->getProperties()[$name] = $value;
    }

    /**
     * Retrieves properties.
     *
     * @return Data
     */
    protected function getProperties()
    {
        if ($this->properties) {
            return $this->properties;
        }

        $classes[] = new ReflectionClass(get_class($this));
        while ($parentClass = reset($classes)->getParentClass()) {
            array_unshift($classes, $parentClass);
        }

        $offsets  = [];
        $readonly = [];
        $rules    = [];

        /** @var ReflectionClass $class */
        foreach ($classes as $class) {
            $comment = $class->getDocComment();
            if (!$comment) continue;

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

                    $offsets[] = $name;

                    if ($isReadonly) {
                        $readonly[] = $name;
                    }

                    if (isset($type)) {
                        $rules[$name] = $type;
                    }
                }
            }
        }

        $this->properties = new Data($offsets, $readonly);
        foreach ($rules as $name => $rule) {
            $this->properties->getValidator()->append($name, $rule);
        }

        return $this->properties;
    }
}