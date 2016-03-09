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
     * @return string
     */
    public function validate($name, $value)
    {
        if (!isset($this->rules[$name])) {
            return null;
        }

        $error = '';
        foreach ($this->rules[$name] as $rule) {
            if (isset($rule['class'])) {
                $class   = $rule['class'];
                $isValid = $value instanceof $class;
            } elseif (isset($rule['callback'])) {
                $callback = $rule['callback'];
                $isValid  = $callback($value);
            }

            if ($isValid) continue;

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

        $item['error'] = sprintf('Validation failed on "%s": ', $name);

        if (is_string($rule)) {
            if (class_exists($rule)) {
                $item['class'] = $rule;
                $item['error'] .= sprintf('instance of "%s" expected, ', $rule) . '"%s" given.';
            } else {
                $type = strtolower($rule);
                if ('required' == $type) {
                    $item['callback'] = function ($value) {
                        return !empty($value);
                    };
                    $item['error'] .= 'value should not be empty.';
                }

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