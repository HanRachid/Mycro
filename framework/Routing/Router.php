<?php

namespace Framework\Routing;

/**
 * Router class, contains array of routes and methods to add routes
 * @package Framework\Routing
 */
class Router
{
    protected array $routes = [];
    public function add(string $method, string $path, callable $handler): Route
    {
        $route = $this->routes[] = new Route($method, $path, $handler);
        return $route;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function dispatch()
    {
        $paths = $this->paths();
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';

    }

    /**
     * extract paths from array of routes
     *
     * @return array
     */
    private function paths(): array
    {
        $paths = [];
        foreach ($this->routes as $route) {
            $paths[] = $route->path();
        }
        return $paths;
    }
    /**
     * match request method and path to route, returns null if no route is found
     *
     * @param string $method
     * @param string $path
     * @return void
     */
    private function match(string $method, string $path): ?Route
    {
        foreach ($this->routes as $route) {
            if($route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }


};
