<?php

use JetBrains\PhpStorm\NoReturn;

class Database
{
    private static PDO $connection;

    /*
    * create database connection
    */
    private function __construct()
    {

    }

    /**
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (!isset(self::$connection)) {
            $host = getenv('DB_HOST', '127.0.0.1');
            $username = getenv('DB_USERNAME', 'rot');
            $password = getenv('DB_PASSWORD', '');
            $database = getenv('DB_DATABASE', 'notifications');
            $port = getenv('DB_PORT', '3306');


            $options = [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ];

            $dsn = "mysql:host=$host;dbname=$database;port=$port;charset=utf8mb4";

            try {
                self::$connection = new \PDO($dsn, $username, $password, $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }

            return self::$connection;
        }
        return self::$connection;
    }

    #[NoReturn] public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    #[NoReturn] public function __sleep()
    {
        trigger_error('Serializing is not allowed.', E_USER_ERROR);
    }

    #[NoReturn] public function __wakeup()
    {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

}
