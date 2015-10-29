<?php
namespace Neat\Loader;

use Neat\Http\Request;

/**
 * Template loader.
 */
class TemplateLoader extends FileLoader
{
    /** @var string */
    protected $basedir;

    /** @var Request */
    protected $request;

    /**
     * Constructor.
     *
     * @param Request $request
     * @param string  $basedir
     */
    public function __construct($basedir, Request $request)
    {
        $this->basedir = $basedir;
        $this->request = $request;
    }

    /**
     * Returns a module location.
     *
     * @param string $module
     *
     * @return array
     */
    public function getLocation($module)
    {
        $location = [];
        $dirs = [];

        if ($this->hasLocation($module)) {
            $location = parent::getLocation($module);
        } elseif ($this->hasLocation('*')) {
            $location = parent::getLocation('*');
        }

        foreach ($location as $pattern) {
            preg_match_all('/{{(\w+)}}/', $pattern, $matches);
            $search = $matches[0];
            $replace = [];

            foreach ($matches[1] as $name) {
                $replace[] = $this->request->get($name);
            }

            $dirs[] = $this->basedir . '/' . str_replace($search, $replace, $pattern);
        }

        return $dirs;
    }

    /**
     * Returns locations.
     *
     * @return array
     */
    public function getLocations()
    {
        $locations = [];
        foreach (array_keys($this->locations) as $domain) {
            $locations[$domain] = $this->getLocation($domain);
        }

        return $locations;
    }
}