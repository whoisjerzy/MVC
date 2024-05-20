<?php
// header('Content-Type: application/json');

use BARTENDER\Classes\Page;
use BARTENDER\Classes\Database;
use BARTENDER\Classes\Router;
use BARTENDER\Middleware\AuthMiddleware;
use BARTENDER\Middleware\RoleMiddleware;
use BARTENDER\Classes\Session;
use BARTENDER\Classes\Authentication;
use BARTENDER\Classes\Functions;
use BARTENDER\Models\UserModel;


define('ROOT_DIR', dirname(__DIR__));

include_once ROOT_DIR . "/vendor/autoload.php";
include_once ROOT_DIR . "/config/config.php";
// Parse JSON input
$requestData = json_decode(file_get_contents('php://input'), true);
// If $requestData is null, set it to an empty array
if ($requestData === null) {
    $requestData = [];
}

$myPage = new Page();
$database = new Database();
$userModel = new UserModel();
// Create instances of the Authentication and Session classes
$session = new Session();
$auth = new Authentication($userModel, $session);

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

$router->dispatchRequest($httpMethod, $uri, $requestData);

$myPage->printPage();
