<?php
namespace Neat\Widget;

/**
 * Layout widget.
 *
 * @property string top
 * @property string left
 * @property string center
 * @property string right
 * @property string bottom
 * @property int    width
 * @property int    leftWidth
 * @property int    rightWidth
 */
class Layout extends AbstractWidget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->width      = 100;
        $this->leftWidth  = 20;
        $this->rightWidth = 20;
    }

    /**
     * Returns the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $top = $this->top;
        $left = $this->left;
        $center = $this->center;
        $right = $this->right;
        $bottom = $this->bottom;
        $width = $this->width ? $this->width . '%' : null;
        $leftWidth = $this->leftWidth ? $this->leftWidth . '%' : null;
        $rightWidth = $this->rightWidth ? $this->rightWidth . '%' : null;

        $margin = '0 %s 0 %s';
        switch (true) {
            case $left && $right:
                $margin = sprintf($margin, $rightWidth, $leftWidth);

                break;

            case $left:
                $margin = sprintf($margin, '0', $leftWidth);

                break;

            case $right:
                $margin = sprintf($margin, $rightWidth, '0');

                break;

            default:
                $margin = '0';
        }

        $html = sprintf('<div style="margin: 0 auto;width: %s;">', $width) . PHP_EOL;

        if ($top) {
            $html .= '<div style="width: 100%;">' . PHP_EOL;
            $html .= $top . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        }

        if ($center) {
            $html .= '<div style="float: left;width: 100%;">' . PHP_EOL;
            $html .= sprintf('<div style="margin: %s;">', $margin). PHP_EOL;
            $html .= $center . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        }

        if ($left) {
            $style = sprintf('float: left;width: %s;', $leftWidth);
            if ($center) $style .= ';margin-left: -100%;';
            $html .= sprintf('<div style="%s">', $style) . PHP_EOL;
            $html .= $left . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        }

        if ($right) {
            $style = sprintf('float: left;width: %s', $rightWidth);
            if (isset($center)) $style .= sprintf(';margin-left: -%s', $rightWidth);
            $html .= sprintf('<div style="%s">', $style)  . PHP_EOL;
            $html .= $right . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        }

        if ($left || $right) {
            $html .= '<div style="clear:both"></div>' . PHP_EOL;
        }

        if ($bottom) {
            $html .= '<div style="width: 100%">' . PHP_EOL;
            $html .= $bottom . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        }

        $html .= '</div>' . PHP_EOL;

        return $html;
    }
}