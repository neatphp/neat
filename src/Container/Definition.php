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
        if (!isset(self::$classes[$className])) {
            self::$classes[$className] = new ReflectionClass($className);
        }

        $this->class = self::$classes[$className];
        $method = $this->class->getConstructor();
        if (!is_null($method)) $this->setMethodInjections($method, $args);
        $this->unique = $unique;

        return $this;
    }

    /**
     * Invokes method.
     *
     * @param string $name
     * @param array  $args
     *
     * @return Definition
     */
    public function __call($name, array $args)
    {
        $method = $this->class->getMethod($name);
        $this->setMethodInjections($method, $args);

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
     * Sets injection methods.
     *
     * @return Definition
     */
    public function methods()
    {
        $args = func_get_args();
        foreach ($args as $methodName) {
            $method = $this->class->getMethod($methodName);
            $this->setMethodInjections($method);
        }

        return $this;
    }

    /**
     * Sets injection properties.
     *
     * @param bool|false $annotationEnabled
     *
     * @return Definition
     */
    public function properties($annotationEnabled = false)
    {
        if ($annotationEnabled) $this->setPropertyInjections();

        $args = func_get_args();
        array_shift($args);

        while (!empty($args)) {
            $propertyName = array_shift($args);
            $propertyValue = array_shift($args);

            $this->assertPropertyExists($propertyName);
            $this->propertyInjections[$propertyName] = $propertyValue;
        }

        return $this;
    }

    /**
     * Returns the class reflection.
     *
     * @return ReflectionClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Returns constructor injections.
     *
     * @return array
     */
    public function getConstructorInjections()
    {
        return $this->constructorInjections;
    }

    /**
     * Returns method injections.
     *
     * @return array
     */
    public function getMethodInjections()
    {
        return $this->methodInjections;
    }

    /**
     * Returns property injections.
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
     * @param string $propertyName
     * @throws Exception\UnexpectedValueException
     */
    private function assertPropertyExists($propertyName)
    {
        if (!$this->class->hasProperty($propertyName) && !array_key_exists($propertyName, $this->propertyInjections)) {
            $msg = sprintf('Property "%s" does not exist in class "%s".', $propertyName, $this->class->getName());
            throw new Exception\UnexpectedValueException($msg);
        }
    }
}