<?php
namespace Neat\Loader;

/**
 * File loader.
 */
class FileLoader
{
    /** @var array */
    protected $locations = [];

    /** @var array */
    protected $placeholders = [];

    /**
     * Retrieves locations.
     *
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Return placeholders.
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Sets locations.
     *
     * @param array $locations
     *
     * @return FileLoader
     */
    public function setLocations(array $locations)
    {
        foreach ($locations as $domain => $dirs) {
            $this->setLocation($domain, $dirs);
        }

        return $this;
    }

    /**
     * Sets a domain location.
     *
     * @param string       $domain
     * @param string|array $dirs
     * @return FileLoader
     */
    public function setLocation($domain, $dirs)
    {
        $this->assertDomainIsNotEmpty($domain);

        $this->locations[$domain] = (array)$dirs;

        return $this;
    }

    /**
     * Sets placeholders.
     *
     * @param array $placeholders
     *
     * @return FileLoader
     */
    public function setPlaceholders(array $placeholders)
    {
        foreach ($placeholders as $name => $value) {
            $this->setPlaceholder($name, $value);
        }

        return $this;
    }

    /**
     * Sets a placeholder.
     *
     * @param string $name
     * @param string $value
     *
     * @return FileLoader
     */
    public function setPlaceholder($name, $value)
    {
        $this->placeholders['{{' . $name . '}}'] = $value;

        return $this;
    }

    /**
     * Locates a file from a domain.
     *
     * @param string $file
     * @param string $domain
     *
     * @return string|false
     */
    public function locate($file, $domain)
    {
        $this->assertDomainIsNotEmpty($domain);
        $this->assertLocationExists($domain);

        $search = $replace = [];
        if ($this->placeholders) {
            $search = array_keys($this->placeholders);
            $replace = array_values($this->placeholders);
        }

        foreach ($this->locations[$domain] as $dir) {
            $dir = str_replace($search, $replace, $dir);
            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_file($path)) return $path;
        }

        return false;
    }

    /**
     * Loads a file from a domain.
     *
     * @param string $file
     * @param string $domain
     *
     * @return string
     */
    public function load($file, $domain)
    {
        $path = $this->locate($file, $domain);
        $this->assertFileIsLocated($path, $file);

        $data = file_get_contents($path);

        return $data;
    }

    /**
     * @param string $domain
     * @throws Exception\UnexpectedValueException
     */
    private function assertDomainIsNotEmpty($domain)
    {
        if (empty($domain)) {
            $msg = 'Domain should not be empty.';
            throw new Exception\UnexpectedValueException($msg);
        }
    }

    /**
     * @param string $domain
     * @throws Exception\OutOfBoundsException
     */
    private function assertLocationExists($domain)
    {
        if (!isset($this->locations[$domain])) {
            $msg = sprintf('Domain location is not set for "%s".', $domain);
            throw new Exception\OutOfBoundsException($msg);
        }
    }

    /**
     * @param string|bool $path
     * @param string $file
     * @throws Exception\UnexpectedValueException
     */
    private function assertFileIsLocated($path, $file)
    {
        if (!$path) {
            $msg = sprintf('File "%s" can not be located.', $file);
            throw new Exception\UnexpectedValueException($msg);
        }
    }
}