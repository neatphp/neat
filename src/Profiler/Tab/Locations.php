<?php
namespace Neat\Profiler\Tab;

use Neat\Loader\ClassLoader;
use Neat\Loader\FileLoader;
use Neat\Loader\PluginLoader;

/**
 * Searching paths.
 */
class Locations extends AbstractTab
{
    /** @var ClassLoader */
    private $classLoader;

    /** @var PluginLoader */
    private $pluginLoader;

    /** @var FileLoader */
    private $fileLoader;

	/**
	 * Constructor.
	 *
	 * @param ClassLoader    $classLoader
     * @param PluginLoader   $pluginLoader
     * @param FileLoader     $fileLoader
	 */
	public function __construct(ClassLoader $classLoader, PluginLoader $pluginLoader, FileLoader $fileLoader)
    {
		$this->classLoader = $classLoader;
        $this->fileLoader = $fileLoader;
        $this->pluginLoader = $pluginLoader;
	}

	/**
	 * Retrieves the content.
	 *
	 * @return string
	 */
	public function getContent()
	{
        $content = '<span>Class Locations: </span>';
        $content .= $this->formatLocations($this->classLoader->getLocations());
        $content .= '<br />';
        $content .= '<span>Plugin Locations: </span>';
        $content .= $this->formatLocations($this->pluginLoader->getLocations());
        $content .= '<br />';
        $content .= '<span>File Locations: </span>';
        $content .= $this->formatLocations($this->fileLoader->getLocations());

		return $content;
	}

    /**
     * Foramts locations
     *
     * @param $locations
     *
     * @return string
     */
    private function formatLocations($locations)
    {
        if (empty($locations)) return '<span>n.a.</span><br />';

        $head = '<thead><tr><th>Domain</th><th>locations</th></thead>';
        $body = '<tbody>';
        $row = '<tr class="%s"><td valign="top">%s</td><td>%s</td></tr>';

        $index = 0;
        foreach ($locations as $domain => $location) {
            $css = $index % 2 ? 'odd' : 'even';
            $body .= sprintf($row, $css, $domain, implode('<br />', $location));
            $index += 1;
        }

        $body .= '</tbody>';
        $table = sprintf('<table class="neat-table">%s%s</table>', $head, $body);

        return $table;
    }
}