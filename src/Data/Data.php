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
    private $factories = [];

    /** @var array */
    private $requiredOffsets = [];

    /** @var Helper\Filter */
    private $filter;

    /** @var Helper\Validator */
    private $validator;

    /** @var bool */
    private $readonly = false;

    /** @var bool */
    private $fixed = false;

    /**
     * Deep cloning.
     */
    public function __clone()
    {
        foreach ($this->values as & $value) {
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
     * @param bool|false $readonly
     * @param bool|false $fixed
     */
    public function __construct($readonly = false, $fixed = false)
    {
        $this->readonly = $readonly;
        $this->fixed = $fixed;
    }

    /**
     * Tells whether an entry exists.
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
     * Returns an entry.
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
            $this->values[$offset] = $this->factories[$offset]($this);
        }

        $value = $this->values[$offset];
        if (isset($value) && isset($this->filter)) {
            $value = $this->filter->filtrate($offset, $value);
            return $value;
        }

        return $this->values[$offset];
    }

    /**
     * Sets an entry.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->assertOffsetNameIsNotEmpty($offset);
        $this->assertDataIsExtendable($offset);
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
     * Deletes an entry.
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
     * Returns an external iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    /**
     * Loads values.
     *
     * @param array $values
     *
     * @return Data
     */
    public function loadValues(array $values)
    {
        $this->values = array_merge($this->values, $values);

        return $this;
    }

    /**
     * Loads offsets.
     *
     * @param array $offsets
     *
     * @return Data
     */
    public function loadOffsets(array $offsets)
    {
        foreach ($offsets as $offset) {
            $this->values[$offset] = null;
        }

        return $this;
    }

    /**
     * Sets an offset as required.
     *
     * @param array $offsets
     *
     * @return Data
     */
    public function requireOffsets(array $offsets)
    {
        foreach ($offsets as $offset) {
            $this->assertOffsetNameIsNotEmpty($offset);
            $this->assertOffsetExists($offset);
            $this->requiredOffsets[] = $offset;
        }

        return $this;
    }

    /**
     * Tells whether an offset path exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        $path = trim($path, '.');
        $offsets = explode('.', $path);
        $lastOffset = array_pop($offsets);

        $ref = $this;
        foreach ($offsets as $offset) {
            if (!isset($ref[$offset])) return false;

            $ref = & $ref[$offset];
        }

        return isset($ref[$lastOffset]);
    }

    /**
     * Returns value from an offset path.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function get($path)
    {
        $path = trim($path, '.');
        $offsets = explode('.', $path);
        $lastOffset = array_pop($offsets);

        $ref = $this;
        foreach ($offsets as $offset) {
            $ref = $ref[$offset];
            if ($ref instanceof Closure) {
                $ref = $ref($this);
            }

            $this->assertReferenceValueIsArray($ref, $path);
        }

        if ($ref[$lastOffset] instanceof Closure) {
            $ref[$lastOffset] = $ref[$lastOffset]($this);
        }

        return $ref[$lastOffset];
    }

    /**
     * Return values.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Sets value to an offset path.
     *
     * @param string $path
     * @param mixed $value
     *
     * @return Data
     */
    public function set($path, $value)
    {
        $path = trim($path, '.');
        $offsets = explode('.', $path);
        $lastOffset = array_pop($offsets);

        $ref = $this;
        foreach ($offsets as $offset) {
            if (!isset($ref[$offset])) $ref[$offset] = [];
            $ref = & $ref[$offset];
            $this->assertReferenceValueIsArray($ref, $path);
        }

        $ref[$lastOffset] = $value;

        return $this;
    }

    /**
     * Sets values.
     *
     * @param array $values
     *
     * @return Data
     */
    public function setValues(array $values)
    {
        $this->assertRequiredOffsetExists($values);

        foreach ($values as $path => $value) {
            $this->set($path, $value);
        }

        return $this;
    }

    /**
     * Resets data.
     *
     * @return Data
     */
    public function reset()
    {
        foreach ($this->values as $key => $value) {
            $this->values[$key] = null;
        }

        return $this;
    }

    /**
     * Sets the loader for lazy loading.
     *
     * @param Helper\Filter $filter
     *
     * @return Data
     */
    public function setFilter(Helper\Filter $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Sets the validator.
     *
     * @param Helper\Validator $validator
     *
     * @return Data
     */
    public function setValidator(Helper\Validator $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Returns the filter.
     *
     * @return Filter
     */
    public function getFilter()
    {
        if (is_null($this->filter)) $this->filter = new Filter;

        return $this->filter;
    }

    /**
     * Returns the validator.
     *
     * @return Validator
     */
    public function getValidator()
    {
        if (is_null($this->validator)) $this->validator = new Validator;

        return $this->validator;
    }

    /**
     * Returns values as an array.
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
     * @param mixed $value
     * @throws Exception\InvalidArgumentException
     */
    private function assertOffsetValueIsValid($offset, $value)
    {
        if (in_array($offset, $this->requiredOffsets) && empty($value)) {
            $msg = sprintf('Value of required offset "%s" should not be empty.', $offset);
            throw new Exception\UnexpectedValueException($msg);
        }

        if (isset($this->validator)) {
            $error = $this->validator->validate($offset, $value);
            if (!empty($error)) {
                throw new Exception\InvalidArgumentException($error);
            }
        }
    }

    /**
     * @param string $offset
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
     * @throws Exception\OverflowException
     */
    private function assertDataIsExtendable($offset)
    {
        if ($this->fixed && !array_key_exists($offset, $this->values)) {
            $msg = sprintf('Data object is fixed, offset "%s" can not be added.', $offset);
            throw new Exception\OverflowException($msg);
        }
    }

    /**
     * @param string $offset
     * @throws Exception\ReadonlyException
     */
    private function assertDataIsOverridable($offset)
    {
        if ($this->readonly && isset($this->values[$offset])) {
            $msg = sprintf('Data object is read-only, offset "%s" can not be overridden.', $offset);
            throw new Exception\ReadonlyException($msg);
        }
    }

    /**
     * @param array $values
     * @throws Exception\UnexpectedValueException
     */
    private function assertRequiredOffsetExists(array $values)
    {
        foreach ($this->requiredOffsets as $offset) {
            if (empty($this[$offset]) && !array_key_exists($offset, $values)) {
                $msg = sprintf('Data "%s" requires offset "%s".', get_class($this), $offset);
                throw new Exception\UnexpectedValueException($msg);
            }
        }
    }

    /**
     * @param mixed $value
     * @param string $path
     * @throws Exception\OutOfBoundsException
     */
    private function assertReferenceValueIsArray($value, $path)
    {
        if (!$value instanceof ArrayAccess && !is_array($value)) {
            $msg = sprintf('Offset path "%s" does not exist in data "%s".', $path, get_class($this));
            throw new Exception\OutOfBoundsException($msg);
        }
    }
}