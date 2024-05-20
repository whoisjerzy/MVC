<?php

namespace BARTENDER\Classes;

use BARTENDER\Models\UserModel;
use BARTENDER\Classes\Session;

class Authentication {
    private $userModel;
    private $session;

    public function __construct(UserModel $userModel, Session $session) {
        $this->userModel = $userModel;
        $this->session = $session;
    }

    public function login(string $username, string $password) {
        if ($this->userModel->verifyPassword($username, $password)) {
            $user = $this->userModel->getUserByUsername($username);
            $this->session->set('user', $user);
            return true;
        }
        return false;
    }
    

    public function createUser(string $username, string $password, string $email, string $role ) {
        if (!empty($username) && !empty($password) && !empty($email) && !empty($role)) {
            return $this->userModel->createUser($username, $password, $email, $role);
        } else {
            throw new \InvalidArgumentException('Username, password, and email are required.');
        }
    }

    public function register($requestData) {
        if (!isset($requestData['username']) || !isset($requestData['password']) || !isset($requestData['email'])) {
        throw new \InvalidArgumentException('Username, password, and email are required.');
        }

        $username = $requestData['username'];
        $password = $requestData['password'];
        $email = $requestData['email'];
        $role = isset($requestData['role']) ? $requestData['role'] : 'user';

        return $this->createUser($username, $password, $email, $role);
    }

    public function logout() {
        $this->session->destroy();
    }

    public function isLoggedIn() {
        return $this->session->get('user') !== null;
    }

    public function isRole($role) {
        $user = $this->session->get('user');
        return $user !== null && $user['role'] === $role;
    }
}