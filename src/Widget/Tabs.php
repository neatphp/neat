<?php
namespace Neat\Widget;

/**
 * Tabs widget.
 *
 * @property array  names
 * @property array  contents
 * @property string contentClass
 */
class Tabs extends AbstractWidget
{
    /**
     * Retrieves the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $contentClass = $this->contentClass;
        if ($contentClass) {
            $contentClass = sprintf('%s-content %s', $this->getDefaultClass(), $contentClass);
        } else {
            $contentClass = sprintf('%s-content', $this->getDefaultClass());
        }

    	$html = sprintf('<ul %s>', $this->getAttributes(['id', 'class', 'style'])) . PHP_EOL;
    	foreach ($this->names as $index => $name) {
            $tabId = sprintf('%s-tab-%s', $this->id, $index);
            $html .= sprintf('<li><a href="#%s">%s</a></li>', $tabId, $name);
    	}
        $html .= '</ul>' . PHP_EOL;

    	foreach ($this->contents as $index => $content) {
            $tabId = sprintf('%s-tab-%s', $this->id, $index);
            $html .= sprintf('<div id="%s" class="%s">', $tabId, $contentClass) . PHP_EOL;
            $html .= $content . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
    	}
        $html .= sprintf('<script type="text/javascript">neat.tabs.init("%s");</script>', $this->id);

    	return $html;
    }
}