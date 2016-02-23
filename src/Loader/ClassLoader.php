<?php
namespace Neat\Loader;

/**
 * Class loader.
 */
class ClassLoader extends FileLoader
{
	/** @var array */
    protected $classMaps = [];

    /**
     * Return class maps.
     *
     * @return array
     */
    public function getClassMaps()
    {
        return $this->getClassMaps();
    }

    /**
     * Sets class maps.
     *
     * @param array $classMaps
     *
     * @return ClassLoader
     */
    public function setClassMaps(array $classMaps)
    {
        foreach ($classMaps as $class => $map) {
            $this->setClassMap($class, $map);
        }

        return $this;
    }

    /**
     * Sets a class map.
     *
     * @param string $class
     * @param string|array  $map
     *
     * @return ClassLoader
     */
    public function setClassMap($class, $map)
    {
        $this->classMaps[$class] = (array)$map;

        return $this;
    }

    /**
     * Locates a class from a namespace.
     *
     * @param string $class
     * @param string $namespace
     *
     * @return string|false
     */
    public function locate($class, $namespace)
    {
        if (0 !== strpos($class, $namespace)) return false;

        $name = trim(str_replace($namespace, '', $class), '\\');
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $name) . '.php';
        $path = parent::locate($file, $namespace);

        return $path;
    }
    
	/**
     * Loads a class from a namespace.
     *
     * @param string $class
     * @param string $namespace
     *
     * @return bool
     */
    public function load($class, $namespace)
	{
        $path = $this->locate($class, $namespace);

        if ($path) {
            require $path;

            return true;
        }

        return false;
	}

    /**
     * Auto-loads class.
     *
     * @param $class
     *
     * @return bool
     */
    public function autoload($class)
    {
        if (isset($this->classMaps[$class])) {
            foreach ($this->classMaps[$class] as $path) require $path;

            return true;
        }

        foreach (array_keys($this->locations) as $namespace) {
            $path = $this->locate($class, $namespace);

            if ($path) {
                if ($path) require $path;

                return true;
            }
        }

        return false;
    }
}