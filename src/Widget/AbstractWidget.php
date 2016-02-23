<?php
namespace Neat\Widget;

use Neat\Base\Object;

/**
 * View widget.
 *
 * @property string id
 * @property string class
 * @property string style
 */
abstract class AbstractWidget extends Object
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = md5(get_class($this) . mt_rand());
        $this->class = $this->getDefaultClass();
    }

    /**
     * Retrieves the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * Retrieves the HTML string.
     *
     * @return string
     */
    public abstract function toHtml();

    /**
     * Retrieves the default css class.
     *
     * @return string
     */
    public function getDefaultClass()
    {
        return str_replace('\\', '-', strtolower(get_class($this)));
    }

    /**
     * Retrieves attributes.
     *
     * @param array $names
     *
     * @return string
     */
    protected function getAttributes(array $names)
    {
        $attributes = [];
        foreach ($names as $index => $name) {
            $propertyName = $name;
            if (is_string($index)) $name = $index;
            $value = $this->$propertyName;
            if ($value) $attributes[] = sprintf('%s="%s"', $name, $value);
        }

        return implode(' ', $attributes);
    }
}