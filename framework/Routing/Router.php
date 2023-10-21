<?php

namespace Framework\Routing;

use Throwable;

/**
 * Router class, contains array of routes and methods to add routes.
 * @package Framework\Routing
 */
class Router
{
    /**
     * @var array routes
     */
    protected array $routes = array();

    /**
     * @var array error_handler
     */
    protected array $error_handler = array(
        '400' => 'Not Allowed',
        '404' => 'Not Found',
    );

    /**
     * adds route to router and returns it.
     *
     * @param string $method route method.
     * @param string $path path method.
     * @param callable $handler callable to execute on route.
     * @return Route
     */
    public function addRoute(
        string $method,
        string $path,
        callable $handler
    ): Route
    {
        $route = $this->routes[] = new Route($method, $path, $handler);
        return $route;
    }

    /**
     *  finds route that matches request.
     *  and returns callable associated
     *  with route. handles client and
     *  server side errors.
     */
    public function dispatch()
    {
        //$paths = $this->paths();
        $request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $request_path = $_SERVER['REQUEST_URI'] ?? '/';
        $matching = $this->match($request_method, $request_path);
        if ($matching !== null) {
            try {
                return $matching->handler();
            } catch (Throwable $e) {
                return 'Server Error';
            }
        }
        if (in_array($request_path, $this->paths())) {
            return $this->notAllowed();
        }
        return $this->notFound();
    }

    /**
     * extracts paths from array of routes.
     *
     * @return array
     */
    private function paths(): array
    {
        $paths = array();
        foreach ($this->routes as $route) {
            $paths[] = $route->path();
        }
        return $paths;
    }

    /**
     * match request method and path to route, null if no route is found.
     *
     * @param string $method route method.
     * @param string $path path method.
     * @return ?Route
     */
    private function match(string $method, string $path): ?Route
    {

        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }

    /**
     *  handler for wrong method.
     *  @return string
     */
    public function notAllowed(): string
    {
        return $this->dispatchError('400');
    }

    /**
     *  handler for route not found;.
     *  @return string
     */
    public function notFound(): string
    {
        return $this->dispatchError('404');
    }

    /**
     * matches error code with associated handler.
     * @param string $code error code
     * @return string
     */
    public function dispatchError(string $code): string
    {
        return $code . ' ' . $this->error_handler[$code];
    }
}
;