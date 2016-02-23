<?php
namespace Neat\Widget;

/**
 * Bar widget.
 *
 * @property string left
 * @property string right
 */
class Bar extends AbstractWidget
{
    /**
     * Retrieves the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
    	$html = sprintf('<div %s>',
                $this->getAttributes(['id', 'class', 'style'])) . PHP_EOL;
        $html .= '<div class="left">' . PHP_EOL;
        $html .= $this->left . PHP_EOL;
        $html .= '</div>' . PHP_EOL;
        $html .= '<div class="right">' . PHP_EOL;
        $html .= $this->right . PHP_EOL;
        $html .= '</div>' . PHP_EOL;
        $html .= '<div style="clear:both;"></div>' . PHP_EOL;
        $html .= '</div>' . PHP_EOL;

        return $html;
    }
}