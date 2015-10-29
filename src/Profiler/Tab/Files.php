<?php
namespace Neat\Profiler\Tab;

/**
 * Included files.
 */
class Files extends AbstractTab
{
	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'Included files';
	}

	/**
	 * Returns the content.
	 *
	 * @return string
	 */
	public function getContent()
	{
		$head = '<thead><tr><th>File</th><th>Size</th><th>Directory</th></thead>';

		$body = '<tbody>';
		$row = '<tr class="%s"><td>%s</td><td>%s</td><td>%s b</td></tr>';
		$files = get_included_files();
        $size = 0;

		foreach ($files as $index => $file) {
			$css = $index % 2 ? 'odd' : 'even';
			$filesize = filesize($file);
			$body .= sprintf($row, $css, basename($file), dirname($file), $filesize);
            $size += $filesize;
		}
		$body .= '</tbody>';

		$foot = '<tfoot><tr><td colspan="3">Total: %s files (%s kb)</td></tfoot>';
		$foot = sprintf($foot, count($files), round($size / 1024));

        $content = sprintf('<table class="neat-table">%s%s%s</table>', $head, $body, $foot);

		return $content;
	}
}