<?php
namespace Neat\Container;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Object definition.
 */
class Definition
{
    /** @var ReflectionClass */
    private $class;

    /** @var array */
    private $constructorInjections = [];

    /** @var array */
    private $methodInjections = [];

    /** @var array */
    private $propertyInjections = [];

    /** @var bool|true */
    private $unique = true;

    /** @var ReflectionClass[] */
    static private $classes;

    /**
     * Constructor.
     *
     * @param string $className
     * @param bool   $unique
     * @param array  $args
     */
    public function __construct($className, $unique, array $args)
    {
        $this->setClass($className);

        $method = $this->class->getConstructor();
        if ($method) {
            $this->setMethodInjections($method, $args);
        }

        if ($this->class->isSubclassOf('Neat\Base\Object')) {
            $this->setPropertyInjections();
        }

        $this->unique = $unique;

        return $this;
    }

    /**
     * Creates an object definition.
     *
     * @param string $className
     *
     * @return Definition
     */
    public static function object($className)
    {
        $args = func_get_args();
        array_shift($args);
        $definition = new Definition($className, false, $args);

        return $definition;
    }

    /**
     * Creates an singleton definition.
     *
     * @param string $className
     *
     * @return Definition
     */
    public static function singleton($className)
    {
        $args = func_get_args();
        array_shift($args);
        $definition = new Definition($className, true, $args);

        return $definition;
    }

    /**
     * Sets a method injection.
     *
     * @param string $name
     *
     * @return self
     */
    public function method($name)
    {
        $args = func_get_args();
        array_shift($args);
        $method = $this->class->getMethod($name);
        $this->setMethodInjections($method, $args);

        return $this;
    }

    /**
     * Sets a property injection.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self
     */
    public function property($name, $value)
    {
        $this->assertPropertyExists($name);

        $this->propertyInjections[$name] = $value;

        return $this;
    }

    /**
     * Retrieves the class reflection.
     *
     * @return ReflectionClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Retrieves constructor injections.
     *
     * @return array
     */
    public function getConstructorInjections()
    {
        return $this->constructorInjections;
    }

    /**
     * Retrieves method injections.
     *
     * @return array
     */
    public function getMethodInjections()
    {
        return $this->methodInjections;
    }

    /**
     * Retrieves property injections.
     *
     * @return array
     */
    public function getPropertyInjections()
    {
        return $this->propertyInjections;
    }

    /**
     * Tells whether object should be unique.
     *
     * @return bool
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * Sets the class reflection.
     *
     * @param $className
     *
     * @return void
     */
    private function setClass($className)
    {
        if (!isset(self::$classes[$className])) {
            self::$classes[$className] = new ReflectionClass($className);
        }

        $this->class = self::$classes[$className];
    }

    /**
     * Sets method injections.
     *
     * @param ReflectionMethod $method
     * @param array            $args
     *
     * @return void
     */
    private function setMethodInjections(ReflectionMethod $method, array $args = [])
    {
        $injections = [];
        $methodName = $method->getName();
        $params = $method->getParameters();

        /** @var ReflectionParameter[] $params */
        foreach ($params as $key => $param) {
            $paramName = $param->getName();
            $paramClass = $param->getClass();

            $paramValue = null;
            if ($param->isOptional())
                $paramValue = $param->getDefaultValue();
            if (isset($paramClass))
                $paramValue = '@' . $paramClass->getName();

            $injections[$paramName] = $paramValue;
            if (isset($args[$key])) $injections[$paramName] = $args[$key];
        }

        if ($method->isConstructor()) {
            $this->constructorInjections = $injections;
        } else {
            $this->methodInjections[$methodName][] = $injections;
        }
    }

    /**
     * Sets property injections.
     *
     * @return void
     */
    private function setPropertyInjections()
    {
        $classes[] = $this->class;
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
                    $this->propertyInjections[$name] = null;

                    if (isset($type)) {
                        if ('\\' == $type[0]) $type = substr($type, 1);
                        if (class_exists($type)) $this->propertyInjections[$name] = '@' . $type;
                    }

                    continue;
                }
            }
        }
    }

    /**
     * @param string $name
     *
     * @throws Exception\UnexpectedValueException
     */
    private function assertPropertyExists($name)
    {
        if (!$this->class->hasProperty($name) && !array_key_exists($name, $this->propertyInjections)) {
            $msg = sprintf('Property "%s" does not exist in class "%s".', $name, $this->class->getName());
            throw new Exception\UnexpectedValueException($msg);
        }
    }
}