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
     * @var
     */
    protected array $parameters;

    /**
     * @var
     */
    protected ?string $name = null;

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
     * getter for route's parameters.
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * extracts params from current path.
     */
    public function extractParams()
    {
        $current_path = $this->path;

        $normalized_server_path = $this->normalizePath($current_path);

        echo $normalized_server_path;
    }

    /**
     * returns true if route matches method and path.
     * @param string $method method to check.
     * @param string $path path to check.
     * @return boolean
     */
    public function matches(string $method, string $path): bool
    {

        // if we find an exact match for path and method, stop here
        if ($this->method === $method && $this->path === $path) {
            return true;
        };

        // next, we'll try to match the user's path with
        // regex, to match named parameters in our routes
        $param_keys = array();

        // we normalize the path  user/JohnDoe/Comments//5/Replies/12
        // becomes /user/JohnDoe/comments/5/replies/12/
        $normalized_server_path = $this->normalizePath($this->path);
        $normalized_user_path = $this->normalizePath($path);

        // we match every occurrence of our regular expression in our route
        // if our path is user/{name}/comments/{commentId}/replies/{replyId?}/
        // then we apply a callback function to each of the substrings contained
        // within {}. if our substring ends with ?, it's optional.
        // finally, we push regex patterns for required and optional params
        // in our regex_parameters array, our path
        // user/{name}/comments/{commentId}/replies/{replyId?}/
        // would fill the array with ['([^/]+)','([^/]+)/','([^/]*)(?:/?)']
        // and our pattern would be
        // /user/([^/]+)/comments/([^/]+)/replies/([^/]*)(?:/?)/
        $pattern = preg_replace_callback(
            '#{([^}]+)}/#',
            function(array $found) use (&$param_keys) {
                //using regex_parameters by reference to modify it
                array_push(
                    $param_keys,
                    rtrim($found[1], '?')
                );

                // if it's an optional parameter, we make the
                // following slash optional as well
                if (str_ends_with($found[1], '?')) {
                    return '([^/]*)(?:/?)';
                }

                return '([^/]+)/';
            },
            $normalized_server_path,
        );

        // if our pattern does not contain + or * (required or optional param),
        // no match found. return false.
        if (!str_contains($pattern, '+') && !str_contains($pattern, '*')) {
            return false;
        }

        // if our regex matches our named route params, we fill our
        // params array with our route's params as key (without curly braces),
        // and the url's params
        if (preg_match("#{$pattern}#", $normalized_user_path, $matches)) {
            foreach ($param_keys as $key=>$param) {
                $this->parameters[$param] = $matches[$key + 1];
            }
            return true;
        };

        // if the url matches no route, named or normal, return false
        return false;
    }

    /**
     *
     * normalizes path to help extract params.
     * @param string $path path to normalize
     * @return string
     */
    public function normalizePath(string $path): string
    {
        // removes all /s from start and end of our path
        // (///pa//th//// becomes pa//th)
        $path = trim($path, '/');

        // add only one / at start and end of out path
        // (pa//th becomes /pa//th/)
        $path = "/{$path}/";

        // regex to replace 2 or more occurrences of /s with one /
        // (/pa//th/ becomes /pa/th/)
        $pattern = '/\/{2,}/';
        $path = preg_replace($pattern, '/', $path);

        return $path;
    }

    /**
     * executes handler.
     * @return string
     */
    public function handler()
    {
        return call_user_func($this->handler);
    }

    /**
     * if route already has a name, return the name, otherwise set.
     * route's name to name in method parameter
     *
     * @param ?string $name null or new name for route
     * @return mixed
     */
    public function name(string $name = null): mixed
    {
        if ($name) {
            $this->name = $name;
            return $this;
        }
        return $this->name;
    }
}
;