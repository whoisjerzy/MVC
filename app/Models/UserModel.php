<?php

namespace BARTENDER\Models;

use BARTENDER\Classes\Database;
use PDOException;

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Fetch all users from the database
     *
     * @return array
     */
    public function getAllUsers()
    {
        try {
            return $this->db->select('SELECT * FROM useraccounts', []);
        } catch (PDOException $e) {
            // Handle exception
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Fetch a user by their ID
     *
     * @param int $id
     * @return array
     */
    public function getUserById($id)
    {
        try {
            return $this->db->select('SELECT * FROM useraccounts WHERE id = ?', [$id]);
        } catch (PDOException $e) {
            // Handle exception
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Fetch a user by their username
     *
     * @param string $username
     * @return array
     */
    public function getUserByUsername(string $username)
        {
            try {
                return $this->db->select('SELECT * FROM useraccounts WHERE username = ?', [$username]);
            } catch (PDOException $e) {
                // Handle exception
                error_log($e->getMessage());
                return [];
            }
        }

    /**
 * Create a new user
 *
 * @param string $username
 * @param string $password
 * @param string $email
 * @param string $role
 * @return array
 */
public function createUser($username, $password,$email, $role)
{
    try {
        // Check if username is already taken
        $existingUser = $this->getUserByUsername($username);
        if ($existingUser) {
            throw new \Exception('Username is already taken');
        }

        // Check if password is strong enough
        if (strlen($password) < 8) {
            throw new \Exception('Password must be at least 8 characters long');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->db->execute('INSERT INTO useraccounts (username, password,email, role) VALUES (?, ?,?,?)', [$username, $hashedPassword, $email, $role]);
        return [];
        
    } catch (PDOException $e) {
        // Handle database exception
        error_log($e->getMessage());
        // You might want to return a specific error message to the user
        return ['success' => false, 'message' => 'Database error. Please try again later.'];
    } catch (\Exception $e) {
        // Handle other exceptions
        error_log($e->getMessage());
        // Return the exception message to the user
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
/**
 * Verify a user's password
 *
 * @param string $username
 * @param string $password
 * @return bool
 */
public function verifyPassword(string $username, string $password) {
    $user = $this->getUserByUsername($username);
    // Check if the user exists and if the password key is set
    if ($user && isset($user[0]['password'])) {
        return password_verify($password, $user[0]['password']);
    } else {
        return false;
    }
}
}
