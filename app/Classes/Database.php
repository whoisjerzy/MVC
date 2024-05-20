<?php

namespace BARTENDER\Classes;

class Database
{
    private $pdo;

    public function __construct()
    {
        // Initialize PDO with appropriate database credentials
        $dsn = "mysql:host={$GLOBALS['server']};dbname={$GLOBALS['dbname']};charset=utf8mb4";
        $username = $GLOBALS['dbuser'];
        $password = $GLOBALS['dbpass'];
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Create PDO instance
        try {
            $this->pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function checkConnection()
    {
        // Check if PDO instance is not null (indicating successful connection)
        return $this->pdo !== null;
    }

    public function select($query, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public function execute($query, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            die("Query execution failed: " . $e->getMessage());
        }
    }
}
