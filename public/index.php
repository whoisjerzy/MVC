<?php

use BARTENDER\Classes\Page;
use BARTENDER\Classes\Database;
use BARTENDER\Classes\Router;
use BARTENDER\Middleware\AuthMiddleware;


use BARTENDER\Classes\Functions;

define('ROOT_DIR', dirname(__DIR__));


include_once ROOT_DIR . "/vendor/autoload.php";
include_once ROOT_DIR . "/config/config.php";





$myPage = new Page();

$database = new Database();


// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$router = new Router();
$router->addMiddleware(new AuthMiddleware());
$router->dispatchRequest($httpMethod, $uri);


$myPage->printPage();
