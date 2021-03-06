<?php
namespace Neat\Http\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SplQueue;

/**
 * Http middleware dispatcher.
 */
class Dispatcher
{
    /** @var SplQueue */
    private $queue;

    /**
     * Appends a middleware.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function append(MiddlewareInterface $middleware)
    {
        if (!$this->queue) {
            $this->queue = new SplQueue;
        }

        $this->queue->push($middleware);

        return $this;
    }

    /**
     * Invokes first middleware in queue.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        if ($this->queue->isEmpty()) {
            return $response;
        }

        /** @var MiddlewareInterface $middleware */
        $middleware = $this->queue->pop();

        return $middleware($request, $response, $this);
    }
}