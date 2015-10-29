<?php
namespace Neat\Widget;

/**
 * Accordion widget.
 *
 * @property array $values
 * @property bool  $hided
 */
class Accordion extends AbstractWidget
{
	/**
	 * Returns the HTML string.
	 *
	 * @return string
	 */
	public function toHtml()
    {
        if ($this->hided) $this->style .= 'display: none;';
        $html = sprintf('<ul %s>',
                $this->getAttributes(['id', 'class', 'style'])) . PHP_EOL;

        foreach ($this->values as $key => $value) {
            $itemNameClass = 'name';
            if (is_array($value)) {
                $itemNameClass .= ' closed';
                $accordion = new Accordion;
                $accordion->id = $this->id . '-' . $key;
                $accordion->values = $value;
                $accordion->hided = true;
                $content = $accordion->toHtml();
            } else {
                $content = '<div class="content">' . PHP_EOL;
                $content .= $value . PHP_EOL;
                $content .= '</div>'. PHP_EOL;
            }

            $html .= '<li>' . PHP_EOL;
            $html .= sprintf('<div class="%s"', $itemNameClass);
            $html .= ' onclick="neat.accordion.toggle(this);">' . PHP_EOL;
            $html .= $key . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
            $html .= $content . PHP_EOL;
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>' . PHP_EOL;

		return $html;
    }
}