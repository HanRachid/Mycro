<?php

use Framework\Routing\Router;

/**
 * function called in index, fills our router with routes
 *
 */
return function (Router $router) {
    $a = fn() => 'hello world';
    $router->add('GET', '/', $a);
};
