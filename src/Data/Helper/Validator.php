<?php
namespace Neat\Data\Helper;

/**
 * Validator.
 */
class Validator
{
    /** @var callable[] */
    private $callbacks = [];

    /** @var string[] */
    private $classes = [];

    /** @var array */
    private $errors = [];

    /**
     * Constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        foreach ($settings as $name => $rule) {
            $this->setRule($name, $rule);
        }
    }

    /**
     * Sets a rule.
     *
     * @param string          $name
     * @param string|callable $rule
     *
     * @return Validator
     */
    public function setRule($name, $rule)
    {
        unset($this->callbacks[$name]);
        unset($this->classes[$name]);

        $this->errors[$name] = sprintf('Validation failed on "%s": ', $name);

        if (is_string($rule)) {
            if (class_exists($rule)) {
                $this->classes[$name] = $rule;
                $this->errors[$name] .= sprintf('instance of "%s" expected, ', $rule) . '"%s" given.';
            }

            $type = strtolower($rule);
            if (false !== strpos($type, '[]')) $type = 'array';
            if (function_exists('is_' . $type)) {
                $this->callbacks[$name] = 'is_' . $type;
                $this->errors[$name] .= sprintf('value of type "%s" expected, ', $type) . '"%s" given.';
            }
        } else {
            $this->setCallback($name, $rule);
            $this->errors[$name] .= 'unexpected value of type "%s" given.';
        }

        return $this;
    }

    /**
     * Validates a value.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return string|null
     */
    public function validate($name, $value)
    {
        $method = str_replace(['.', '-', '_'], ' ', strtolower($name));
        $method = 'validate' . str_replace(' ', '', ucwords($method));

        switch (true) {
            case method_exists($this, $method):
                $return = $this->$method($value);

                break;

            case isset($this->callbacks[$name]):
                $return = $this->callbacks[$name]($value);

                break;

            case isset($this->classes[$name]) && !$value instanceof $this->classes[$name]:
                return $this->getError($name, $value);

            default:
                return null;
        }

        if (is_string($return)) return $return;

        if (false === $return) return $this->getError($name, $value);

        return null;
    }

    /**
     * Returns an error message.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return string
     */
    private function getError($name, $value)
    {
        $type = is_object($value) ? get_class($value) : gettype($value);

        return sprintf($this->errors[$name], $type);
    }

    /**
     * Sets a callback.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return Validator
     */
    private function setCallback($name, callable $callback)
    {
        $this->callbacks[$name] = $callback;

        return $this;
    }
}