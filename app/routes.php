<?php

use Framework\Routing\Router;

/**
 * function called in index, fills our router with routes
 *
 */
return function(Router $router) {

    $router->addRoute('GET', '/', fn() => 'hello world');
    $router->addRoute('GET',
        'user/{name}/comments/{commentId}/replies/{replyId?}/',
        fn() => 'hello '
    );
    $router->addRoute('GET',
        'foo/{boo}/bar/{far}/baz/{faz?}/',
        fn() => $router->currentRoute()->parameters()['boo']
    );
};