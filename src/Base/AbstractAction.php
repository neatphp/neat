<?php
namespace Neat\Base;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * ADR action.
 *
 * @property-read \Psr\Http\Message\RequestInterface request
 * @property-read \Neat\Base\AbstractResponder       responder
 */
abstract class AbstractAction extends Component
{
    /**
     * @return Response
     */
    abstract public function __invoke();

    /**
     * @param Request       $request
     * @param Response      $response
     * @param callable|null $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next = null);
}