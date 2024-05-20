<?php
// Middleware/AuthMiddleware.php

namespace BARTENDER\Middleware;

class AuthMiddleware
{
    public function handle($handler)
    {


        $uri = $_SERVER['REQUEST_URI'];

        // Define roles required for specific routes
        $roleRequirements = [
            '/admin' => 'admin',
            '/user' => 'user',
            // Add other routes and their required roles here
        ];

        // Exclude API routes from redirection
        if (strpos($uri, '/api/v1') === 0) {
            // Check if the user is not logged in for API routes
            if (!isset($_SESSION['user'])) {
                // Return an error response
                http_response_code(401); // Unauthorized
                echo json_encode(array("message" => "Unauthorized"));
                exit;
            }

            if ($_SESSION['user'][0]['role'] !== 'admin') {
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
}
