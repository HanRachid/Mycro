<?php

use Framework\Routing\Router;

/**
 * function called in index, fills our router with routes
 *
 */
return function(Router $router) {
    $router->addRoute('POST', '/', fn() => 'hello world');
};