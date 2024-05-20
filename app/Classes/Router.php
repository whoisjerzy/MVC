<?php

namespace BARTENDER\Classes;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\simpleDispatcher;
use BARTENDER\Middleware\AuthMiddleware;
use BARTENDER\Middleware\RoleMiddleware;

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


    public function dispatchRequest(string $httpMethod, string $uri, array $requestData)
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

                // Check if handler is an array and get the first element
                if (is_array($handler)) {
                    $handler = $handler[0];
                }

                // Split handler into class and method
                list($class, $method) = explode('@', $handler, 2);

                // Instantiate the controller class
                $controller = new $class();

                // Add the request data to the vars array
                $vars = array_merge($vars, $requestData);

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
                $r->addRoute('GET', '/users/{id:\d+}', ['BARTENDER\Controllers\UserController@show', AuthMiddleware::class]);
                // Add more routes as needed
            });

            $r->addGroup('/admin', function (RouteCollector $r) {
                // Define your API routes here
                $r->addRoute('GET', '', ['BARTENDER\Controllers\PageController@admin', AuthMiddleware::class]);
                // Add more routes as needed
            });

            // Add the protected route with middleware
            $r->addRoute('GET', '/login', 'BARTENDER\Controllers\PageController@login');
            $r->addRoute('GET', '/register', 'BARTENDER\Controllers\PageController@register');
            $r->addRoute('POST', '/login', 'BARTENDER\Controllers\AuthController@login');
            $r->addRoute('POST', '/register', 'BARTENDER\Controllers\AuthController@register');
            $r->addRoute('GET', '/logout', 'BARTENDER\Controllers\AuthController@logout');
            $r->addRoute('GET', '/status', 'BARTENDER\Controllers\AuthController@checkLoginStatus');
            $r->addRoute('GET', '/', 'BARTENDER\Controllers\PageController@index');
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
