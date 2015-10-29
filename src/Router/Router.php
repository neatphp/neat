<?php
namespace Neat\Router;

use Neat\Base\Component;
use Neat\Http\Request;

/**
 * Router routes the incoming request.
 *
 * @property \Neat\Http\Request $request
 */
class Router extends Component
{
    /** @var Route[] */
	private $routes = [];

    /**
     * Constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        foreach ($settings as $key => $setting) {
            $name = is_string($key) ? $key : null;
            $this->addRoute($setting, $name);
        }
    }

    /**
     * Returns a route by name.
     *
     * @param string $name
     *
     * @return Route
     */
    public function getRoute($name)
    {
        $this->assertRouteExists($name);

        return $this->routes[$name];
    }

    /**
     * Adds a route.
     *
     * @param string|array $setting
     * @param string|null  $name
     *
     * @return Router
     */
    public function addRoute($setting, $name = null)
    {
        if (is_string($setting)) {
            $route = new Route($setting);
        } else {
            $setting = new RouteSetting($setting);
            $route = new Route($setting->pattern);

            $route->getUrlParams()
                ->setValues($setting->defaultValues)
                ->requireOffsets($setting->requiredParams);

            $methods = $route->getHttpMethods();
            foreach ($setting->httpMethods as $method) {
                $methods[$method] = true;
            }
        }

        if (isset($name)) {
            $this->routes[$name] = $route;
        } else {
            $this->routes[] = $route;
        }

        return $this;
    }

    /**
     * Routes the HTTP Request.
     *
     * @return Request
     */
    public function route()
    {
        $uri = $this->request->getPathInfo();
        $this->dispatchEvent('router.pre_route', ['request' => $this->request]);

        foreach ($this->routes as $route) {
            $this->dispatchEvent('router.pre_match', ['route' => $route]);
            $matched = $route->match($uri);
            $this->dispatchEvent('router.post_match', ['route' => $route]);

            if ($matched) {
                $this->request->pathParams->setValues($route->getUrlParams()->toArray());

                break;
            }
        }

        $this->dispatchEvent('router.post_route', ['request' => $this->request]);

        return $this->request;
    }

    /**
     * @param string $name
     * @throws Exception\OutOfBoundsException
     */
    private function assertRouteExists($name)
    {
        if (!isset($this->routes[$name])) {
            $msg = sprintf('Route "%s" does not exist.', $name);
            throw new Exception\OutOfBoundsException($msg);
        }
    }
}