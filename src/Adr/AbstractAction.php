<?php
namespace Neat\Base;

/**
 * ADR action.
 *
 * @property      \Psr\Http\Message\RequestInterface request
 * @property-read \Neat\Base\AbstractResponder       responder
 */
abstract class AbstractAction extends Component
{
    /**
     * Method will be called when a script tries to call action as a function.
     *
     * @param callable|null $next
     */
    public function __invoke(callable $next = null)
    {
        $this->handle();

        if ($next) {
            return $next($this->request);
        }
    }

    /**
     * @return mixed
     */
    abstract protected function handle();
}