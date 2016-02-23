<?php
namespace Neat\Widget;

/**
 * Input widget.
 *
 * @property string name
 * @property string value
 * @property string type
 * @property string label
 * @property string labelClass
 * @property string error
 * @property string errorClass
 */
class Input extends AbstractWidget
{
    /**
     * Retrieves the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $html = sprintf('<label %s>%s</label> ',
                $this->getAttributes(['class' => 'labelClass', 'for' => 'id']), $this->label) . PHP_EOL;
        $html .= sprintf('<input %s>', $this->getAttributes(['class', 'style', 'type', 'value'])) . PHP_EOL;
        if ($this->error) $html .= sprintf('<div class="%s">%s</div>',
                $this->errorClass, $this->error) . PHP_EOL;

    	return $html;
    }
}