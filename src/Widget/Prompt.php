<?php
namespace Neat\Widget;

/**
 * Prompt widget.
 */
class Prompt extends Form
{
	/**
	 * Returns the HTML string.
	 *
	 * @return string
	 */
	public function toHtml()
    {
        $dialog = new Dialog;
        $dialog->bottom = new Bar;
        $dialog->center = $this->content;
        $dialog->bottom->right = $this->cancel->toHtml() . $this->confirm->toHtml();
        $this->content = $dialog->toHtml();

        return parent::toHtml();
    }
}