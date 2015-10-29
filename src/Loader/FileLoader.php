<?php
namespace Neat\Loader;

/**
 * File loader.
 */
class FileLoader
{
    /** @var array */
    protected $locations = [];

    /**
     * Tells whether a domain location exists.
     *
     * @param string $domain
     *
     * @return bool
     */
    public function hasLocation($domain)
    {
        return isset($this->locations[$domain]);
    }

    /**
     * Returns a domain location.
     *
     * @param string $domain
     *
     * @return array
     */
    public function getLocation($domain)
    {
        $this->assertDomainIsNotEmpty($domain);
        $this->assertLocationExists($domain);

        return $this->locations[$domain];
    }

    /**
     * Returns locations.
     *
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Sets a domain location.
     *
     * @param string $domain
     * @param array  $dirs
     *
     * @return FileLoader
     */
    public function setLocation($domain, array $dirs)
    {
        $this->locations[$domain] = $dirs;

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
        foreach ($this->getLocation($domain) as $dir) {
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