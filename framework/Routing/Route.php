<?php

namespace Framework\Routing;

/**
 * Route class, has method, path and handler.
 */
class Route
{
    /**
     * @var
     */
    protected string $method;

    /**
     * @var
     */
    protected string $path;

    /**
     * @var
     */
    protected $handler;

    /**
     *  Route class constructor.
     *
     * @param string $method new route's method.
     * @param string $path new route's path.
     * @param callable $handler callable function associated with route.
     * @return
     */
    public function __construct(string $method, string $path, $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
    }

    /**
     * stored route's path getter.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * stored route's method getter.
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * returns true if route matches method and path.
     *
     * @param string $method method to check.
     * @param string $path path to check.
     * @return boolean
     */
    public function matches(string $method, string $path): bool
    {
        return $this->method === $method && $this->path === $path;
    }

    /**
     * executes handler.
     *
     * @return string
     */
    public function handler()
    {
        return call_user_func($this->handler);
    }
}
;