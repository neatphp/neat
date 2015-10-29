<?php
namespace Neat\Base;

use Neat\Data\Data;
use ReflectionClass;

/**
 * Object.
 */
class Object
{
    /** @var Data */
    private $properties;

    /** @var bool|true */
    protected $readonly = false;

    /** @var bool|true */
    protected $fixed = true;

    /**
     * Deep cloning.
     */
    public function __clone()
    {
        $this->properties = clone $this->properties;
    }

    /**
     * Returns value of a property or path.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getProperties()->get($name);
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
        $this->getProperties()->set($name, $value);
    }

    /**
     * Returns properties.
     *
     * @return Data
     */
    public function getProperties()
    {
        if (is_null($this->properties)) {
            $this->properties = new Data($this->readonly, $this->fixed);
            $validator = $this->properties->getValidator();

            $classes[] = new ReflectionClass(get_class($this));
            while ($parentClass = reset($classes)->getParentClass()) {
                array_unshift($classes, $parentClass);
            }

            /** @var ReflectionClass $class */
            foreach ($classes as $class) {
                $comment = $class->getDocComment();
                if (!$comment) continue;

                $lines = explode(PHP_EOL, $comment);
                foreach ($lines as $line) {
                    $pos = stripos($line, '@property');
                    if (false !== $pos) {
                        $property = explode(' ', preg_replace('/[ ]+/', ' ', substr($line, $pos)));

                        if (3 == count($property)) {
                            $type = $property[1];
                            $name = $property[2];
                        } else {
                            $type = null;
                            $name = $property[1];
                        }

                        if ('$' == $name[0]) $name = substr($name, 1);
                        $this->properties->loadOffsets([$name]);
                        if (isset($type)) $validator->setRule($name, $type);
                    }
                }
            }
        }

        return $this->properties;
    }
}