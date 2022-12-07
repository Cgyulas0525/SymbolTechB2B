<?php

class mySQLDatabase {

    protected $connection = null;

    public function __construct() {
        try {
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE,
            );
            $dsn = 'mysql:host='. MYSQL_HOST .';dbname='. MYSQL_DATABASE .';charset='. MYSQL_CHARSET;
            $this->connection = new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD, $opt);
        } catch(PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function select($query = "" , $params = [])
    {
        try {
            $stmt = $this->executeStatement( $query , $params );
            $result = $stmt->fetchAll();

            return $result;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    private function executeStatement($query = "" , $params = [])
    {
        if (!$params) {
            try {
                return $this->connection->query($query);
            } catch(PDOException $e) {
                die('Failed: ' . $e->getMessage() . " " . $query);
            }
        }
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            die('Failed: ' . $e->getMessage());
        }
    }

    public function modelSelect($modelName) {
        $sql = 'SELECT * FROM ' . $modelName;
        try {
            return $this->select($sql);
        } catch (PDOException $e) {
            die('Failed: ' . $e->getMessage());
        }
    }
}



