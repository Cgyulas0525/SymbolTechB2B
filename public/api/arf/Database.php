<?php

require_once "Utility.php";

class DB
{
    protected static $instance = null;

    protected function __construct() {}
    protected function __clone() {}

    /*
     * new PDO create
     *
     * @return $intance > new PDO
     */
    public static function instance()
    {
        try {
            if (self::$instance === null) {
                $opt  = array(
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => FALSE,
                );
                $dsn = 'mysql:host='. Utility::envLoader('DB_HOST') .';dbname='. Utility::envLoader('DB_DATABASE') .';charset='. Utility::envLoader('DB_CHARSET');
                self::$instance = new PDO($dsn, Utility::envLoader('DB_USERNAME'), Utility::envLoader('DB_PASSWORD'), $opt);
            }
            return self::$instance;
        } catch(PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit();
        }
    }

    /*
     * Call a callback with an array of parameters
     *
     * @return value of the callback, or false on error.
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    /*
     * Own select run
     *
     * @return $smtp
     */
    public static function run($sql, $args = [])
    {
        if (!$args) {
            try {
                return self::instance()->query($sql);
            } catch(PDOException $e) {
                echo 'Failed: ' . $e->getMessage();
                exit();
            }
        }
        try {
            $stmt = self::instance()->prepare($sql);
            $stmt->execute($args);
            return $stmt;
        } catch(PDOException $e) {
            echo 'Failed: ' . $e->getMessage();
            exit();
        }
    }

    /*
     * Count of records, from select
     *
     * @param $sql - mySQl select
     *
     * @return integer
     */
    public static function countRecord($sql) {
        $smtp = DB::run($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) > 0) {
                foreach ($record as $row) {
                    $db = $row['db'];
                }
                return $db;
            }
        } else {
            return 0;
        }
    }

    /*
     * Id of record, from Name of record
     *
     * @param $table - table name
     * @param $name
     *
     * @return integer
     */
    public static function fromNameToId($table, $name) {
        $sql = "SELECT Id FROM " . $table . " WHERE Name = '" . $name . "'";
        $smtp = DB::run($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) > 0) {
                foreach ($record as $row) {
                    $Id = $row['Id'];
                }
                return $Id;
            }
        } else {
            return NULL;
        }
    }
}

