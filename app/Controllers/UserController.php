<?php

namespace BARTENDER\Controllers;

use BARTENDER\Models\UserModel;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Retrieve list of users from the UserModel
        $users = $this->userModel->getAllUsers();
        return json_encode($users);
    }

    public function show($id)
    {
        // Retrieve user with the given ID from the UserModel        
        $user = $this->userModel->getUserById($id);
        return json_encode($user);
    }
}
