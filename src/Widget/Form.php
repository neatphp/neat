<?php
namespace Neat\Widget;

/**
 * Form widget.
 *
 * @property string              action
 * @property string              method
 * @property string              content
 * @property \Neat\Widget\Submit confirm
 * @property \Neat\Widget\Submit cancel
 * @property \Neat\Widget\Form   nextStep
 * @property \Neat\Widget\Form   previousStep
 *
 */
class Form extends AbstractWidget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->action  = htmlspecialchars($_SERVER['PHP_SELF']);
        $this->method  = 'post';

        $this->confirm          = new Submit;
        $this->confirm->name    = md5($this->id . '-confirm');
        $this->confirm->content = 'Confirm';
        $this->cancel           = new Submit;
        $this->cancel->name     = $this->id . '-cancel';
        $this->cancel->content  = 'Cancel';
    }

    /**
     * Retrieves the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        $html = '';

        if ($this->confirm->isClicked()) {
            if ($this->nextStep) $html = $this->nextStep->toHtml();

            return $html;
        }

        if ($this->cancel->isClicked()) {
            if ($this->previousStep) $html = $this->previousStep->toHtml();

            return $html;
        }

        $html = sprintf('<form %s>',
                $this->getAttributes(['class', 'style', 'action', 'method'])) . PHP_EOL;
        $html .= $this->content . PHP_EOL;
        foreach ($_REQUEST as $key => $value) {
            $html .= sprintf('<input type="hidden" name="%s" value="%s">', $key, $value) . PHP_EOL;
        }
        $html .= '</form>' . PHP_EOL;

        return $html;
    }
}