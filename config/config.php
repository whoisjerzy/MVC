<?php
// Load environment variables from .env file
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    $env = explode("\n", $env);
    foreach ($env as $line) {
        if (!empty($line)) {
            list($key, $value) = explode('=', $line);
            $_ENV[$key] = $value;
        }
    }
}

// Assign variables from environment
$server = $_ENV['SERVER'] ?? 'default_server';
$dbuser = $_ENV['DBUSER'] ?? 'default_username';
$dbpass = $_ENV['DBPASS'] ?? 'default_password';
$dbname = $_ENV['DBNAME'] ?? 'default_database_name';
