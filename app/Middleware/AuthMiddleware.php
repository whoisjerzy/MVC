<?php
// Middleware/AuthMiddleware.php

namespace BARTENDER\Middleware;

class AuthMiddleware
{
    public function handle($handler)
    {
        // Exclude API routes from redirection
        if (strpos($_SERVER['REQUEST_URI'], '/api/v1') === 0) {
            // Check if the user is not logged in for API routes
            if (!isset($_SESSION['user'])) {
                // Return an error response
                http_response_code(401); // Unauthorized
                echo json_encode(array("message" => "Unauthorized"));
                exit;
            }
        } else {
            // Redirect to login page for non-API routes
            if ($_SERVER['REQUEST_URI'] !== '/login') {
                // Check if the user is not logged in
                if (!isset($_SESSION['user'])) {
                    // Redirect to login page
                    header('Location: /login');
                    exit;
                }
            }
        }

        // Continue to the next middleware or route handler
        return $handler;
    }
}
