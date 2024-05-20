<?php

namespace BARTENDER\Models;

use BARTENDER\Classes\Database;

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllUsers()
    {
        return $this->db->select('SELECT * FROM useraccounts', []);
    }

    public function getUserById($id)
    {
        return $this->db->select('SELECT * FROM useraccounts WHERE id = ?', [$id]);
    }
}
