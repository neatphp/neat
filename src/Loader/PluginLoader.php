<?php
namespace Neat\Loader;

use ReflectionClass;

/**
 * Plugin loader.
 */
class PluginLoader extends TemplateLoader
{
    /** @var array */
    protected $classes = [];

    /** @var array */
    protected $namespaces = [];

    /**
     * Locates a plugin, which implements/extends a superclass.
     *
     * @param string $name
     * @param string $superclass
     *
     * @return string|false
     */
    public function locate($name, $superclass)
    {
        $this->assertPluginNameIsNotEmpty($name);

        $superclass = trim($superclass, '\\');
        $file = $this->getClassName($name) . '.php';
        $path = parent::locate($file, $superclass);

        return $path;
    }
    
	/**
     * Loads the plugin, which implements/extends a superclass.
     * It will first try to find the class, which is declared in the located file,
     * then checks whether the class implements/extends the given superclass.
     *
     * @param string $name
     * @param string $superclass
     *
     * @return string
     */
    public function load($name, $superclass)
    {
        if (!isset($this->classes[$superclass][$name])) {
            $path = $this->locate($name, $superclass);

            if (false === $path) {
                $namespace = $this->getNamespace($superclass);
                $className = $this->getClassName($name);
                $this->classes[$superclass][$name] = $namespace . '\\' . $className;
            } else {
                $classes = get_declared_classes();
                require $path;
                $diff = array_diff(get_declared_classes(), $classes);
                $class = end($diff);

                $this->assertClassIsDeclared($class, $path);
                $this->assertClassIsPlugin($name, $class, $superclass);

                $this->classes[$superclass][$name] = $class;
            }
        }

        return $this->classes[$superclass][$name];
	}

    /**
     * Returns the class name.
     *
     * @param string $pluginName
     *
     * @return mixed
     */
    private function getClassName($pluginName)
    {
        $className = str_replace(array('_', '-', '.'), ' ', $pluginName);
        $className = str_replace(' ', '', ucwords($className));

        return $className;
    }

    /**
     * Returns the namespace.
     *
     * @param string $superclass
     *
     * @return string
     */
    private function getNamespace($superclass)
    {
        $superclass = trim($superclass, '\\');
        if (!isset($this->namespaces[$superclass])) {
            $class = new ReflectionClass($superclass);
            $this->namespaces[$superclass] = $class->getNamespaceName();
        }

        return $this->namespaces[$superclass];
    }

    /**
     * @param string $name
     * @throws Exception\UnexpectedValueException
     */
    private function assertPluginNameIsNotEmpty($name)
    {
        if (empty($name)) {
            $msg = 'Plugin name should not be empty.';
            throw new Exception\UnexpectedValueException($msg);
        }
    }

    /**
     * @param string|false $class
     * @param string $path
     * @throws Exception\UnexpectedValueException
     */
    private function assertClassIsDeclared($class, $path)
    {
        if (false === $class) {
            $msg = sprintf('No class is declared in file "%s".', $path);
            throw new Exception\UnexpectedValueException($msg);
        }
    }

    /**
     * @param string $name
     * @param string $class
     * @param string $superclass
     * @throws Exception\DomainException
     */
    private function assertClassIsPlugin($name, $class, $superclass)
    {
        $reflectionClass = new ReflectionClass($class);
        if (!$reflectionClass->isSubclassOf($superclass)) {
            $msg = sprintf('Plugin "%s" does not implements/extends the superclass "%s".', $name, $superclass);
            throw new Exception\DomainException($msg);
        }
    }
}