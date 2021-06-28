<?php

require_once('db.php');

// Singleton to connect to database.
class Database
{
    // The class instance.
    private static $instance = null;

    // The database instance.
    private $connection;

    // The database connection settings.
    private $host = DB_HOST;
    private $user = DB_USERNAME;
    private $pass = DB_PASSWORD;
    private $name = DB_NAME;

    // Represents a database.
    private function __construct()
    {
        try {
            // The database connection is established in the private constructor.
            $this->connection = new PDO("mysql:host={$this->host}; dbname={$this->name}", $this->user, $this->pass,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    // Gets the database instance.
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    // Gets the database connection.
    public function getConnection()
    {
        return $this->connection;
    }

    // Closes the database connection.
    public function closeConnection()
    {
        try {
            self::$instance = null;
            $this->connection = null;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}