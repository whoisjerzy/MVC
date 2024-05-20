<?php
// Middleware/AuthMiddleware.php
// Middleware/AuthMiddleware.php

namespace BARTENDER\Middleware;

class AuthMiddleware
{
    public function handle($handler)
    {
        $uri = $_SERVER['REQUEST_URI'];

        // Debug: Print request URI
        echo "Request URI: $uri\n";

        // Define roles required for specific routes
        $roleRequirements = [
            '/admin' => 'admin'
            // Add other routes and their required roles here
        ];

        // Debug: Print role requirements
        echo "Role Requirements: ";
        print_r($roleRequirements);

        // Define permissions for specific actions or resources
        // Define permissions for specific actions or resources
        $permissions = [
            '/api/v1/users' => ['admin'],
            '/api/v1/user/{id:\d+}' => ['admin'],
            // Add more permissions as needed
        ];


        // Debug: Print permissions
        echo "Permissions: ";
        print_r($permissions);

        // Exclude API routes from redirection
        if (strpos($uri, '/api/v1') === 0) {
            // Check if the user is not logged in for API routes
            if (!isset($_SESSION['user'])) {
                // Return an error response
                http_response_code(401); // Unauthorized
                echo json_encode(array("message" => "Unauthorized"));
                exit;
            }

            // Debug: Print user role
            echo "User Role: {$_SESSION['user'][0]['role']}\n";

            // Check if the logged-in user has the required permissions for API routes
            $routePermission = $this->getRoutePermission($uri, $permissions);

            // Debug: Print route permission
            echo "Route Permission: ";
            print_r($routePermission);

            if ($routePermission && !in_array($_SESSION['user'][0]['role'], $routePermission)) {
                // Return an error response
                http_response_code(403); // Forbidden
                echo json_encode(array("message" => "Forbidden: You do not have permission to access this resource."));
                exit;
            }
        } else {
            // Redirect to login page for non-API routes
            if ($uri !== '/login') {
                // Check if the user is not logged in
                if (!isset($_SESSION['user'])) {
                    // Redirect to login page
                    header('Location: /login');
                    exit;
                }

                // Check if the current route requires a specific role
                foreach ($roleRequirements as $route => $role) {
                    if (strpos($uri, $route) === 0 && (!isset($_SESSION['user'][0]['role']) || $_SESSION['user'][0]['role'] !== $role)) {
                        // Redirect to an error page or show an error message
                        http_response_code(403); // Forbidden
                        echo "Forbidden: You do not have permission to access this page.";
                        exit;
                    }
                }
            }
        }

        // Continue to the next middleware or route handler
        return $handler;
    }

    // Helper function to get the required permissions for a route


    private function getRoutePermission($route, $permissions)
    {
        foreach ($permissions as $action => $roles) {
            $pattern = '/^' . str_replace(['/', '{id:\d+}'], ['\/', '(\d+)'], $action) . '$/';
            if (preg_match($pattern, $route)) {
                return $roles;
            }
        }
        return null;
    }
}
