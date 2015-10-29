<?php
namespace Neat\Container;

use Closure;

/**
 * DI container.
 */
class Container
{
    /** @var array */
    private $objects = [];

    /** @var array */
    private $factories = [];

    /** @var Definition[] */
    private $definitions;

	/** @var array */
	private $dependencies = [];

    /**
     * Constructor.
     *
     * @param array     $settings
     * @param bool|true $autowiring
     */
    public function __construct(array $settings = [], $autowiring = true)
    {
        foreach ($settings as $id => $value) {
            if (is_string($value)) $value = Definition::singleton($value);
            if (is_int($id) && is_object($value)) {
                if ($value instanceof Definition) {
                    $id = $value->getClass()->getName();
                } else {
                    $id = get_class($value);
                }
            }

            $this->set($id, $value);
        }
    }

    /**
     * Returns an entry.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        $this->assertIdIsNotEmpty($id);
        $this->assertEntryOrClassExists($id);
        $this->assertNoCircularDependency($id);

        $this->dependencies[$id] = true;

        if (!isset($this->objects[$id]) && isset($this->factories[$id])) {
            $this->objects[$id] = $this->factories[$id]($this);
        }

        if (isset($this->objects[$id])) {
            $object = $this->objects[$id];
        } else {
            if (!isset($this->definitions[$id])) {
                $this->definitions[$id] = Definition::object($id);
                if ($this->definitions[$id]->getClass()->isSubclassOf('Neat\Base\Component')) {
                    $this->definitions[$id]->properties(true);
                }
            }

            $object = $this->makeObject($this->definitions[$id]);
            if ($this->definitions[$id]->isUnique()) $this->objects[$id] = $object;
        }

        unset($this->dependencies[$id]);

        return $object;
    }

    /**
     * Sets an entry.
     *
     * @param string $id
     * @param mixed  $value
     *
     * @return Container
     */
    public function set($id, $value)
    {
        $this->assertValueIsObject($value, $id);
        $this->assertEntryIsOverridable($id);

        if ($value instanceof Closure) {
            $this->factories[$id] = $value;

            return $this;
        }

        if ($value instanceof Definition) {
            $this->definitions[$id] = $value;

            return $this;
        }

        $this->objects[$id] = $value;

        return $this;
    }

    /**
     * Makes an object.
     *
     * @param Definition $definition
     *
     * @return mixed
     */
    private function makeObject(Definition $definition)
    {
        $class = $definition->getClass();
        $constructorInjections = $this->parseReference($definition->getConstructorInjections());
        $methodInjections = $this->parseReference($definition->getMethodInjections());
        $propertyInjections = $this->parseReference($definition->getPropertyInjections());

        $object = $class->newInstanceArgs($constructorInjections);

        foreach ($methodInjections as $methodName => $injections) {
            foreach ($injections as $args) {
                $class->getMethod($methodName)->invokeArgs($object, $args);
            }
        }

        foreach ($propertyInjections as $name => $value) {
            if ($class->hasProperty($name)) {
                $property = $class->getProperty($name);
                $property->setAccessible(true);
                $property->setValue($object, $value);
            } else {
                $object->$name = $value;
            }
        }

        return $object;
    }

    /**
     * Parses reference.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function parseReference($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'parseReference'], $value);
        }

        if (is_string($value) && 0 === strpos($value, '@')) {
            $segments = explode(':', substr($value, 1));
            $id = array_shift($segments);
            $method = array_shift($segments);
            $object = $this->get($id);

            return isset($method) ? call_user_func_array(array($object, $method), $segments) : $object;
        }

        return $value;
    }

    /**
     * @param string $id
     * @throws Exception\UnexpectedValueException
     */
    private function assertIdIsNotEmpty($id)
    {
        if (empty($id)) {
            $msg = 'Entry ID should not be empty.';
            throw new Exception\UnexpectedValueException($msg);
        }
    }

    /**
     * @param string $id
     * @throws Exception\OutOfBoundsException
     */
    private function assertEntryOrClassExists($id)
    {
        if (!isset($this->objects[$id]) &&
            !isset($this->definitions[$id]) &&
            !isset($this->factories[$id]) &&
            !class_exists($id)) {
            $msg = sprintf('No entry or class found for "%s"', $id);
            throw new Exception\OutOfBoundsException($msg);
        }
    }

    /**
     * @param mixed $value
     * @param string $id
     * @throws Exception\InvalidArgumentException
     */
    private function assertValueIsObject($value, $id)
    {
        if (!is_object($value)) {
            $msg = sprintf('Invalid value for "%s", an object expected, "%s" given.', $id, gettype($value));
            throw new Exception\InvalidArgumentException($msg);
        }
    }

    /**
     * @param string $id
     * @throws Exception\ReadonlyException
     */
    private function assertEntryIsOverridable($id)
    {
        if (isset($this->objects[$id])) {
            $msg = sprintf('Entry "%s" has already a value and can not be overridden .', $id);
            throw new Exception\ReadonlyException($msg);
        }
    }

    /**
     * @param string $id
     * @throws Exception\CircularDependencyException
     */
    private function assertNoCircularDependency($id)
    {
        if (isset($this->dependencies[$id])) {
            $msg = 'Circular dependency detected: "%s".';
            $msg = sprintf($msg, implode('->', array_keys($this->dependencies)));
            throw new Exception\CircularDependencyException($msg);
        }
    }
}