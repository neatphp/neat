<?php
namespace Neat\Widget;

/**
 * Dialog widget.
 *
 * @property \Neat\Widget\Overlay overlay
 */
class Dialog extends Box
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->overlay = new Overlay;
    }

    /**
     * Retrieves the HTML string.
     *
     * @return string
     */
	public function toHtml()
    {
        if (empty($this->top->right)) {
            $style = 'style="cursor:pointer;"';
            $onclick = sprintf('onclick="neat.toggle(\'%s\', \'%s\');"', $this->id, $this->overlay->id);
            $this->top->right = sprintf('<strong %s %s>X</strong>', $style, $onclick);
        }

        return $this->overlay . parent::toHtml();
    }
}