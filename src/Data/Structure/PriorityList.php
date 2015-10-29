<?php
namespace Neat\Data\Structure;

use ArrayIterator;
use IteratorAggregate;

/**
 * Priority list.
 */
class PriorityList implements IteratorAggregate
{
    /** @var array */
    private $values = [];

    /**
     * Returns an external iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Inserts a value.
     *
     * @param mixed $value
     * @param int   $priority
     *
     * @return PriorityList
     */
    public function insert($value, $priority = 0)
    {
        $this->values[$priority][] = [$value, $priority];

        return $this;
    }

    /**
     * Removes a value.
     *
     * @param mixed $value
     *
     * @return PriorityList
     */
    public function remove($value)
    {
        foreach ($this->values as & $columns) {
            while(false !== $key = array_search($value, array_column($columns, 0))) {
                unset($columns[$key]);
            }
        }

        return $this;
    }

    /**
     * Returns a random value.
     *
     * @return mixed
     */
    public function random()
    {
        $values = [];
        $priorities = [];
        foreach ($this->values as $columns) {
            foreach ($columns as $column) {
                $values[] = $column[0];
                $priorities[] = $column[1];
            }
        }

        $key = 0;
        $sum = array_sum($priorities);
        $random = mt_rand(0, $sum);
        foreach ($priorities as $key => $priority) {
            if ($random <= $priority)  break;
            $random -= $priority;
        }

        return $values[$key];
    }

    /**
     * Returns values as an array.
     *
     * @return array
     */
    public function toArray()
    {
        krsort($this->values);
        $values = $this->values;
        $values = call_user_func_array('array_merge', $values);
        foreach ($values as & $value) {
            $value = $value[0];
        }

        return $values;
    }
}