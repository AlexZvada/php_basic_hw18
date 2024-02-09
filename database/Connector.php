<?php

class Connector
{
    /**
     * @var PDO
     */
    private static PDO $instance;
    protected function __construct() { }

    /**
     * @return void
     */
    protected function __clone() { }

    /**
     * @return mixed
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (!isset(self::$instance)){
            $dsn = DB_DRIVER . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            self::$instance = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ
            ]);
        }

        return self::$instance;
    }
}