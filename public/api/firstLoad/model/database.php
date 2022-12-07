<?php

class database {

    protected $connection = null;

    public function __construct() {
        try {
            $this->connection = new PDO(DB_DNS, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit;
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
                die('Failed: ' . $e->getMessage());
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
        $sql = 'SELECT * FROM "' . $modelName .'" ORDER BY "Id"';
        try {
            return $this->select($sql);
        } catch (PDOException $e) {
            die('Failed: ' . $e->getMessage());
        }
    }
}


