<?php
namespace Neat\Widget;

/**
 * Box widget.
 *
 * @property \Neat\Widget\Bar top
 * @property string center
 * @property \Neat\Widget\Bar bottom
 */
class Box extends AbstractWidget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->top    = new Bar;
        $this->bottom = new Bar;
    }

    /**
     * Retrieves the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $top = $this->top;
        $bottom = $this->bottom;

        $html = sprintf('<div %s>',
                $this->getAttributes(['id', 'class', 'style'])) . PHP_EOL;
		if($top && ($top->left || $top->right)) $html .= $top->toHtml() . PHP_EOL;
        $html .= '<div class="center">' . PHP_EOL;
        $html .= $this->center . PHP_EOL;
        $html .= '</div>' . PHP_EOL;
        if($bottom && ($bottom->left || $bottom->right)) $html .= $bottom->toHtml() . PHP_EOL;
        $html .= '</div>' . PHP_EOL;

		return $html;
    }
}