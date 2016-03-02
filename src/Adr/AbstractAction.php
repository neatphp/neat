<?php
namespace Neat\Adr;

use Neat\Base\Component;

/**
 * ADR action.
 *
 * @property      \Psr\Http\Message\RequestInterface request
 * @property-read \Neat\Base\AbstractResponder       responder
 * @property-read \Neat\Adr\AbstractAction           beforeAction
 * @property-read \Neat\Adr\AbstractAction           afterAction
 */
abstract class AbstractAction extends Component
{
    /**
     * Method will be called when a script tries to call action as a function.
     */
    public function __invoke()
    {
        if ($this->beforeAction) {
            $action = $this->beforeAction;
            $action->setProperty('request', $this->request);
            $action();
        }

        $this->handle();

        if ($this->afterAction) {
            $action = $this->afterAction;
            $action->setProperty('request', $this->request);
            $action();
        }
    }

    /**
     * @return mixed
     */
    abstract protected function handle();
}