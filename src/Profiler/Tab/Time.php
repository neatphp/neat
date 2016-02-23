<?php
namespace Neat\Profiler\Tab;

use Neat\Util\Timer;

/**
 * Time records.
 */
class Time extends AbstractTab
{
    /** @var Timer */
    private $timer;

    /**
     * Constructor.
     *
     * @param Timer $timer
     */
    public function __construct(Timer $timer)
    {
        $this->timer = $timer;
    }

	/**
	 * Retrieves the name.
	 *
	 * @return string
	 */
	public function getName()
	{
		$seconds = round((microtime(true) - $_SERVER['REQUEST_TIME']), 2);
		return sprintf('Page execution time (%s s)', $seconds);
	}

	/**
	 * Retrieves the content.
	 *
	 * @return string
	 */
	public function getContent()
	{
		$records = $this->timer->getRecords();
		if (empty($records)) return 'No timer was started!';

		$head = '<thead><tr><th>Name</th><th>Calls</th><th>Average</th></thead>';
		$body = '<tbody>';
		$row = '<tr class="%s"><td>%s</td><td valign="top">%s</td><td valign="top">%s s</td></tr>';

        $index = 0;
		foreach ($records as $name => $record) {
			$css = $index % 2 ? 'odd' : 'even';
			$calls = $this->timer->getCalls($name);
			$body .= sprintf($row, $css, $name, $calls, round($record, 2));
			$index += 1;
		}

		$body .= '</tbody>';
        $content = sprintf('<table class="neat-table">%s%s</table>', $head, $body);

		return $content;
	}
}