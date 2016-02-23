<?php
namespace Neat\Profiler;

use Neat\Util\Dumper;

/**
 * Profiler.
 */
class Profiler
{
    /** @var Dumper */
    private $dumper;

    /** @var array */
    private $debugRecords = [];

    /** @var Tab\AbstractTab[] */
    private $tabs = [];

    /** @var string */
    private $activeTabId;

    /** @var bool|true */
    private $debugEnabled = true;

    /**
     * Constructor.
     *
     * @param Dumper    $dumper
     * @param bool|true $debugEnabled
     */
    public function __construct(Dumper $dumper, $debugEnabled = true)
    {
        $this->dumper = $dumper;
        $this->debugEnabled = $debugEnabled;
    }

    /**
     * Retrieves debug records.
     *
     * @return array
     */
    public function getDebugRecords()
    {
        return $this->debugRecords;
    }

    /**
     * Retrieves the id of active tab.
     *
     * @return string
     */
    public function getActiveTabId()
    {
        return $this->activeTabId;
    }

    /**
     * Adds a debug record.
     *
     * @param mixed  $variable
     * @param string $description
     * @param string $file
     * @param int    $line
     *
     * @return Profiler
     */
    public function debug($variable, $description = null, $file = null, $line = null)
    {
        if ($this->debugEnabled) {
            $record['variable'] = $variable;
            $record['description'] = $description;
            $record['file'] = $file;
            $record['line'] = $line;
            $this->debugRecords[] = $record;
        }

        return $this;
    }

    /**
     * Sets tabs.
     *
     * @param Tab\AbstractTab[] $tabs
     *
     * @return Profiler
     */
    public function setTabs(array $tabs)
    {
        foreach ($tabs as $tab) $this->addTab($tab);

        return $this;
    }

    /**
     * Adds a tab.
     *
     * @param Tab\AbstractTab $tab
     * @param bool|false      $active
     *
     * @return Profiler
     */
    public function addTab(Tab\AbstractTab $tab, $active = false)
    {
        $this->tabs[] = $tab;
        if ($active) $this->activeTabId = $tab->getId();

        return $this;
    }

    /**
     * Renders profiler template and injects output.
     *
     * @param string $html
     *
     * @return string
     */
    public function render($html)
    {
        ob_start();
        include sprintf('%s/etc/template/profiler.phtml', realpath(__DIR__ . '/..'));
        $output = ob_get_clean();

        $count = 0;
        $html = str_ireplace('</body>', $output . '</body>', $html, $count);

        if (!$count) $html = $output . $html;

        return $html;
    }
}