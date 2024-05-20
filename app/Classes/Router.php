<?php

namespace BARTENDER\Classes;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\simpleDispatcher;
use BARTENDER\Middleware\AuthMiddleware;

class Router
{
    private $dispatcher;
    private $middlewares = [];

    public function __construct()
    {
        $this->dispatcher = $this->createDispatcher();
    }

    public function addMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function dispatchRequest(string $httpMethod, string $uri)
    {
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // 404 Not Found
                $this->renderNotFoundPage();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // 405 Method Not Allowed
                echo '405 Method Not Allowed';
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // Apply middlewares
                if ($this->requiresAuthentication($handler)) {
                    foreach ($this->middlewares as $middleware) {
                        $handler = $middleware->handle($handler);
                    }
                }

                // Split handler into class and method
                list($class, $method) = explode('@', $handler, 2);

                // Instantiate the controller class
                $controller = new $class();

                // Call the controller method
                echo call_user_func_array([$controller, $method], $vars);
                break;
        }
    }

    private function createDispatcher(): Dispatcher
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $r->addGroup('/api/v1', function (RouteCollector $r) {
                // Define your API routes here
                $r->addRoute('GET', '/users', ['BARTENDER\Controllers\UserController@index', AuthMiddleware::class]);
                $r->addRoute('GET', '/users/{id:\d+}', 'BARTENDER\Controllers\UserController@show');
                // Add more routes as needed
            });

            // Add the protected route with middleware
            $r->addRoute('GET', '/login', 'BARTENDER\Controllers\PageController@login');
            $r->addRoute('GET', '/register', 'BARTENDER\Controllers\PageController@register');
            $r->addRoute('GET', '/', 'BARTENDER\Controllers\PageController@index');
            $r->addRoute('GET', '/admin', 'BARTENDER\Controllers\PageController@admin');
        });

        return $dispatcher;
    }
    private function requiresAuthentication($handler)
    {
        // Check if the handler is an array and the middleware is required
        return is_array($handler) && in_array(AuthMiddleware::class, $handler);
    }
    private function renderNotFoundPage()
    {
        // Initialize the View class with the path to your view files
        $view = new View(__DIR__ . '/../Views');

        // Render the _404 view file
        echo $view->render('errors/_404', ['tite', 'Error'], false);
    }
}
