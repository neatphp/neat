<?php
namespace Neat\Widget;

/**
 * Overlay widget.
 */
class Overlay extends AbstractWidget
{
    /**
     * Returns the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $html = sprintf('<div %s></div>', $this->getAttributes(['id', 'class', 'style'])). PHP_EOL;
        $html .= sprintf('<script>neat.overlay.adjust("%s")</script>', $this->id) . PHP_EOL;

    	return $html;
    }
}