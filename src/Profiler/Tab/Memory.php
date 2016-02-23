<?php
namespace Neat\Profiler\Tab;

/**
 * Memory usage.
 */
class Memory extends AbstractTab
{
	/**
	 * Retrieves the content.
	 *
	 * @return string
	 */
	public function getContent()
	{
		$head = '<thead><tr><th>Type</th><th>Size</th></thead>';
		$body = '<tbody>';
		$row = '<tr class="%s"><td>%s</td><td>%s</td></tr>';
		$body .= sprintf($row, 'even', 'Memory usage', round(memory_get_usage() / 1024) . ' kb');
		$body .= sprintf($row, 'odd', 'Memory peak usage', round(memory_get_peak_usage() / 1024) . ' kb');
		$body .= sprintf($row, 'even', 'Memory limit', ini_get('memory_limit'));
		$body .= '</tbody>';
        $content = sprintf('<table class="neat-table">%s%s</table>', $head, $body);

		return $content;
	}
}