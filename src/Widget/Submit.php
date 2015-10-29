<?php
namespace Neat\Widget;

/**
 * Submit widget.
 *
 * @property string name
 * @property string value
 * @property string content
 */
class Submit extends AbstractWidget
{
    /**
     * Returns the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $html = sprintf('<button type="submit" %s>%s</button>',
            $this->getAttributes(['class', 'style', 'name', 'value']), $this->content);

        return $html;
    }

    /**
     * Tells whether submit button has been clicked.
     *
     * @return bool
     */
    public function isClicked()
    {
        return isset($_REQUEST[$this->name]) && $this->value == $_REQUEST[$this->name];
    }
}