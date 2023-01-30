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

    public function executeStatement($query = "" , $params = [])
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


    public function executeStatementReturnFail($query = "" , $params = [])
    {
        if (!$params) {
            try {
                return $this->connection->query($query);
            } catch(PDOException $e) {
                return "Failed: " . $e->getMessage() . " " . $query;
            }
        }
        try {
            $stmp = $this->connection->prepare($query);
            $stmp->execute($params);
            return $stmp;
        } catch(PDOException $e) {
            return "Failed: " . $e->getMessage();
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

    /*
     * Count of records, from select
     *
     * @param $sql - mySQl select
     *
     * @return integer
     */
    public function countRecord($modelName = "", $where = "") {
        $sql = "SELECT Count(*) AS db FROM " . $modelName;
        $sql .= $where != "" ? " WHERE " . $where : $where;
        $smtp = $this->executeStatement($sql);
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
    public function fromNameToId($table = "", $name = "") {
        $sql = "SELECT Id FROM " . $table . " WHERE Name = '" . $name . "'";
        $smtp = $this->executeStatement($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) > 0) {
                foreach ($record as $row) {
                    $Id = $row['Id'];
                }
                return $Id;
            } else {
                return "Error: not found record!";
            }
        } else {
            return "Error: not found record!";
        }
    }

    /*
     * Id of record, record
     *
     * @param $table - table name
     * @param $id - integer
     *
     * @return record
     */
    public function recordFromId($table = "", $id = -9999) {
        $sql = "SELECT * FROM " . $table . " WHERE Id = '" . $id . "'";
        $smtp = $this->executeStatement($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) > 0) {
                foreach ($record as $row) {
                    $record = $row;
                }
                return $record;
            } else {
                return "Error: not found record!";
            }
        } else {
            return "Error: not found record!";
        }
    }

}



