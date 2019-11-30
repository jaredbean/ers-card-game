<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 */

class DatabaseConnection
{
    private static $instance = null;
//    private static $host = "sql201.epizy.com";
//    private static $dbname = "epiz_24388369_cs3750_names";
//    private static $user = "epiz_24388369";
//    private static $pass = "weberstudent1";

    private static $host = "icarus.cs.weber.edu";
    private static $dbname = "W01085178";
    private static $user = "W01085178";
    private static $pass = "Jaredcs!";

    private function __construct()
    {

    }

    public static function getInstance(): \PDO
    {
        if (!static::$instance === null) {
            return static::$instance;
        } else {
            try {
                $connectionString = "mysql:host=".static::$host.";dbname=".static::$dbname;
                static::$instance = new \PDO($connectionString, static::$user, static::$pass);
                static::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return static::$instance;
            } catch (PDOException $e) {
                echo "Unable to connect to the database: " . $e->getMessage();
                die();
            }
        }
    }
}