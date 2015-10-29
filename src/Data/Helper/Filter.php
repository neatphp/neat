<?php
namespace Neat\Data\Helper;

/**
 * Filter.
 */
class Filter
{
    /** @var callable[] */
    private $callbacks = [];

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
     * @param string   $name
     * @param callable $rule
     *
     * @return Filter
     */
    public function setRule($name, callable $rule)
    {
        $this->callbacks[$name] = $rule;

        return $this;
    }

    /**
     * Filtrates a value.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function filtrate($name, $value)
    {
        $method = str_replace(['.', '-', '_'], ' ', strtolower($name));
        $method = 'filtrate' . str_replace(' ', '', ucwords($method));
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        if (isset($this->callbacks[$name])) {
            return $this->callbacks[$name]($value);
        }

        return $value;
    }
}