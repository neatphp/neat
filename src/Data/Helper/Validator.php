<?php
namespace Neat\Data\Helper;

/**
 * Validator.
 */
class Validator
{
    /** @var array */
    private $rules = [];

    /**
     * Validates a value.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return string|null
     */
    public function validate($name, $value)
    {
        if (!isset($this->rules[$name])) {
            return null;
        }

        $error = null;
        foreach ($this->rules[$name] as $rule) {
            $valid = true;
            if (isset($rule['class'])) {
                $class = $rule['class'];
                $valid = $value instanceof $class;
            } elseif (isset($rule['callback'])) {
                $callback = $rule['callback'];
                $valid = $callback($value);
            }

            $not = $rule['not'];
            if ($valid && !$not || !$valid && $not) {
                continue;
            }

            $type = is_object($value) ? get_class($value) : gettype($value);
            $error = sprintf($rule['error'], $type);
        }

        return $error;
    }

    /**
     * Appends a rule.
     *
     * @param string          $name
     * @param string|callable $rule
     *
     * @return self
     */
    public function append($name, $rule)
    {
        if (!isset($this->rules[$name])) {
            $this->rules[$name] = [];
        }

        $item['not']   = false;
        $item['error'] = sprintf('Validation failed on "%s": ', $name);

        if (is_string($rule)) {
            if ('!' == $rule[0]) {
                $item['not'] = true;
                $rule = trim(substr($rule, 1));
            }

            if (class_exists($rule)) {
                $item['class'] = $rule;
                $item['error'] .= sprintf('instance of "%s" expected, ', $rule) . '"%s" given.';
            } else {
                $type = strtolower($rule);
                if (false !== strpos($type, '[]')) {
                    $type = 'array';
                }

                $function = 'is_' . $type;
                if (function_exists($function)) {
                    $item['callback'] = $function;
                    $item['error'] .= sprintf('value of type "%s" expected, ', $type) . '"%s" given.';
                }
            }
        } else {
            $item['callback'] = $rule;
            $item['error'] .= 'unexpected value of type "%s" given.';
        }

        $this->rules[$name][] = $item;

        return $this;
    }

    /**
     * Removes rules.
     *
     * @param string $name
     *
     * @return self
     */
    public function remove($name)
    {
        unset($this->rules[$name]);

        return $this;
    }
}