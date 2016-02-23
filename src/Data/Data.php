<?php
namespace Neat\Data;

use ArrayAccess;
use ArrayIterator;
use Closure;
use IteratorAggregate;
use Neat\Data\Helper\Filter;
use Neat\Data\Helper\Validator;

/**
 * Data object holds data as an array.
 */
class Data implements ArrayAccess, IteratorAggregate
{
    /** @var array */
    private $values = [];

    /** @var array */
    private $readonly = [];

    /** @var array */
    private $factories = [];

    /** @var Helper\Filter */
    private $filter;

    /** @var Helper\Validator */
    private $validator;

    /**
     * Operations to perform on clone.
     */
    public function __clone()
    {
        foreach ($this->values as &$value) {
            if (is_object($value)) $value = clone $value;
        }

        if ($this->filter) {
            $this->filter = clone $this->filter;
        }

        if ($this->validator) {
            $this->validator = clone $this->validator;
        }
    }

    /**
     * Constructor.
     *
     * @param array $offsets
     * @param array $readonly
     */
    public function __construct(array $offsets, array $readonly = [])
    {
        foreach ($offsets as $offset) {
            $this->values[$offset] = null;
        }

        $this->readonly = $readonly;
    }

    /**
     * Tells whether an offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->assertOffsetNameIsNotEmpty($offset);

        return isset($this->values[$offset]) || isset($this->factories[$offset]);
    }

    /**
     * Retrieves value of an offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function & offsetGet($offset)
    {
        $this->assertOffsetNameIsNotEmpty($offset);
        $this->assertOffsetExists($offset);

        if (!isset($this->values[$offset]) && isset($this->factories[$offset])) {
            $value = $this->factories[$offset]($this);
            $this->assertOffsetValueIsValid($offset, $value);

            $this->values[$offset] = $value;
        }

        $value = $this->values[$offset];
        if (isset($value) && $this->filter) {
            $value = $this->filter->filtrate($offset, $value);
        }

        return $value;
    }

    /**
     * Sets value of an offset.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->assertOffsetNameIsNotEmpty($offset);
        $this->assertOffsetExists($offset);
        $this->assertDataIsOverridable($offset);

        if ($value instanceof Closure) {
            $this->factories[$offset] = $value;
            $this->values[$offset] = null;
        } else {
            $this->assertOffsetValueIsValid($offset, $value);
            $this->values[$offset] = $value;
        }
    }

    /**
     * Deletes an offset.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->assertOffsetNameIsNotEmpty($offset);

        unset($this->values[$offset]);
    }

    /**
     * Retrieves an external iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Loads values.
     *
     * @param array $values
     *
     * @return self
     */
    public function load(array $values)
    {
        foreach ($values as $offset => $value) {
            $this[$offset] = $value;
        }

        return $this;
    }

    /**
     * Resets data.
     *
     * @param string|null $offset
     *
     * @return self
     */
    public function reset($offset = null)
    {
        if ($offset) {
            $this->assertOffsetExists($offset);

            $this->values[$offset] = null;
        } else {
            foreach (array_keys($this->values) as $offset) {
                $this->values[$offset] = null;
            }
        }

        return $this;
    }

    /**
     * Retrieves the filter.
     *
     * @return Filter
     */
    public function getFilter()
    {
        if (!isset($this->filter)) $this->filter = new Filter;

        return $this->filter;
    }

    /**
     * Retrieves the validator.
     *
     * @return Validator
     */
    public function getValidator()
    {
        if (!isset($this->validator)) $this->validator = new Validator;

        return $this->validator;
    }

    /**
     * Retrieves data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $values = [];
        foreach (array_keys($this->values) as $offset) {
            $value = $this[$offset];
            $values[$offset] = $value instanceof Data ? $value->toArray() : $value;
        }

        return $values;
    }

    /**
     * @param string $offset
     *
     * @throws Exception\UnexpectedValueException
     */
    private function assertOffsetNameIsNotEmpty($offset)
    {
        if (empty($offset)) {
            $msg = 'Offset name should not be empty.';
            throw new Exception\UnexpectedValueException($msg);
        }
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @throws Exception\InvalidArgumentException
     */
    private function assertOffsetValueIsValid($offset, $value)
    {
        if ($this->validator) {
            $error = $this->validator->validate($offset, $value);
            if ($error) {
                throw new Exception\InvalidArgumentException($error);
            }
        }
    }

    /**
     * @param string $offset
     *
     * @throws Exception\OutOfBoundsException
     */
    private function assertOffsetExists($offset)
    {
        if (!array_key_exists($offset, $this->values)) {
            $msg = sprintf('Offset "%s" does not exist in data object.', $offset);
            throw new Exception\OutOfBoundsException($msg);
        }
    }

    /**
     * @param string $offset
     *
     * @throws Exception\ReadonlyException
     */
    private function assertDataIsOverridable($offset)
    {
        if (in_array($offset, $this->readonly) && isset($this->values[$offset])) {
            $msg = sprintf('Offset "%s" is read-only and can not be overridden.', $offset);
            throw new Exception\ReadonlyException($msg);
        }
    }
}