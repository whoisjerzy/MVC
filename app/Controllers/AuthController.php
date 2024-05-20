<?php

namespace BARTENDER\Controllers;

use BARTENDER\Classes\Authentication;
use BARTENDER\Classes\Session;
use BARTENDER\Models\UserModel;

class AuthController
{
    private $auth;

    public function __construct()
    {
        $session = new Session();
        $userModel = new UserModel();
        $this->auth = new Authentication($userModel, $session);
    }
    public function register($username, $password, $email, $role)
    {
        // $username = $_POST['username'];
        // $password = $_POST['password'];
        // $email = $_POST['email'];
        // Additional validation and sanitization of input data can be done here

        if ($this->auth->createUser($username, $password, $email, $role)) {
            echo 'User registered successfully';
        } else {
            echo 'Failed to register user';
        }
    }
    public function login($username, $password)
    {

        if ($this->auth->login($username, $password)) {
            echo 'Logged in successfully';
        } else {
            echo 'Invalid username or password';
        }
    }

    public function checkLoginStatus()
    {
        if ($this->auth->isLoggedIn()) {
            echo 'User is logged in as a ' . $_SESSION["user"][0]["role"];
        } else {
            echo 'User is not logged in';
        }
    }

    public function logout()
    {
        $this->auth->logout();
        echo 'Logged out successfully';
    }
}
