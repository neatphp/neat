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
    private $offsets = [];

    /** @var array */
    private $lazyloads = [];

    /** @var Helper\Filter */
    private $filter;

    /** @var Helper\Validator */
    private $validator;

    /**
     * Operations to perform on clone.
     */
    public function __clone()
    {
        foreach ($this->values as $key => $value) {
            if (is_object($value)) $this->values[$key] = clone $value;
        }

        if ($this->filter) {
            $this->filter = clone $this->filter;
        }

        if ($this->validator) {
            $this->validator = clone $this->validator;
        }
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

        return isset($this->offsets[$offset]);
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

        $this->lazyload($offset);
        $this->validate($offset);
        $value = $this->filtrate($offset);

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
        $this->assertOffsetIsOverridable($offset);

        $this->values[$offset] = $value;
        $this->validate($offset);
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
        unset($this->offsets[$offset]);
        unset($this->lazyloads[$offset]);
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
     * Initiates a offset.
     *
     * @param string     $offset
     * @param mixed|null $value
     * @param bool|false $readonly
     *
     * @return self
     */
    public function init($offset, $value = null, $readonly = false)
    {
        $this->assertOffsetNameIsNotEmpty($offset);

        $this->values[$offset]  = $value;
        $this->offsets[$offset] = $readonly;

        if ($value instanceof Closure) {
            $this->lazyloads[$offset] = $value;
            $this->values[$offset]    = null;
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
        foreach (array_keys($this->offsets) as $offset) {
            $value = $this[$offset];
            $values[$offset] = $value instanceof Data ? $value->toArray() : $value;
        }

        return $values;
    }

    /**
     * Lazy-loads value of offset.
     *
     * @param string $offset
     *
     * @return void
     */
    private function lazyload($offset)
    {
        if (isset($this->lazyloads[$offset]) && (!$this->offsets[$offset] || !isset($this->values[$offset]))) {
            $this->values[$offset] = $this->lazyloads[$offset]($this);
        }
    }

    /**
     * Filtrates value of offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    private function filtrate($offset)
    {
        $value = $this->values[$offset];
        if (isset($value) && $this->filter) {
            $value = $this->filter->filtrate($offset, $value);
        }

        return $value;
    }

    /**
     * Validates value of offset.
     *
     * @param string $offset
     *
     * @return void
     *
     * @throws Exception\InvalidArgumentException
     */
    private function validate($offset)
    {
        if ($this->validator) {
            $error = $this->validator->validate($offset, $this->values[$offset]);
            if ($error) {
                throw new Exception\InvalidArgumentException($error);
            }
        }
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
     *
     * @throws Exception\OutOfBoundsException
     */
    private function assertOffsetExists($offset)
    {
        if (!isset($this->offsets[$offset])) {
            $msg = sprintf('Offset "%s" does not exist in data object.', $offset);
            throw new Exception\OutOfBoundsException($msg);
        }
    }

    /**
     * @param string $offset
     *
     * @throws Exception\ReadonlyException
     */
    private function assertOffsetIsOverridable($offset)
    {
        if ($this->offsets[$offset] && isset($this->values[$offset])) {
            $msg = sprintf('Offset "%s" is read-only and can not be overridden.', $offset);
            throw new Exception\ReadonlyException($msg);
        }
    }
}