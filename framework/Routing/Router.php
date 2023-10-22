<?php

namespace Framework\Routing;

use Exception;
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
     * @var Route current route
     */
    protected Route $current_route;

    /**
     * @var array error_handler
     */
    protected array $error_handler = array(

    );

    /**
     *  getter for current_route.
     */
    public function currentRoute()
    {
        return $this->current_route;
    }

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
        $paths = $this->paths();
        $request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $request_path = $_SERVER['REQUEST_URI'] ?? '/';
        $matching_route = $this->match($request_method, $request_path);
        if ($matching_route) {
            try {
                //found matching route
                $this->current_route = $matching_route;
                return $matching_route->handler();
            } catch (Throwable $e) {
                //server error
                return $this->serverError();
            }
        }

        // path associated with different method
        if (in_array($request_path, $paths)) {
            return $this->notAllowed();
        }

        // no matching route
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
        $handler = fn () => 'Not Allowed';
        return $this->dispatchError('400', $handler);
    }

    /**
     *  handler for route not found.
     *  @return string
     */
    public function notFound(): string
    {
        $handler = fn () => 'Not Found';
        return $this->dispatchError('404', $handler);
    }

    /**
     * handler for server error.
     * @return string
     */
    public function serverError(): string
    {
        $handler = fn () => 'Server Error';
        return $this->dispatchError('500', $handler);
    }

    /**
     * matches error code with associated handler.
     * @param string $code error code
     * @param callable $handler associated with error
     * @return string
     */
    public function dispatchError(string $code, callable $handler): string
    {
        $this->error_handler[$code] ??= $handler;
        return $code . ' ' . call_user_func($handler);
    }

    /**
     * creates route from name.
     * @param string $name name of route
     * @param array $params name of parameters
     */
    public function route(string $name, array $params)
    {
        foreach ($this->routes as $route) {
            if ($route->name() === $name) {
                $finds = array();
                $replaces = array();
                foreach ($params as $key => $value) {
                    // replace required params with value
                    array_push($finds, "{{$key}}");
                    array_push($replaces, $value);

                    // replace optional params with value with a ?
                    array_push($finds, "{{$key}?}");
                    array_push($replaces, $value);
                }
                $path = $route->path();
                $path = str_replace($finds, $replaces, $path);

                // remove not provided optional params
                $path = preg_replace('#{[^}]+}#', '', $path);

                return $path;
            }
        }
        throw new Exception('No route with this name');
    }
}
;