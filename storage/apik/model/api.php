<?php

class api
{
    public $id;
    public $filename;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    function __construct() {
        $this->pdo = new mySQLDatabase();
        $this->date = date('Y-m-d H:i:s', strtotime('now'));
    }

    public function selectApiId($file) {
        $sql = "SELECT id FROM api WHERE filename = '" . $file . "' AND created_at = ( SELECT MAX(created_at) FROM api WHERE filename = '". $file . "')";
        $smtp = $this->pdo->executeStatement($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) == 1) {
                foreach ($record as $row) {
                    $id = $row['id'];
                }
                $this->id = $id;
            }
        } else {
            $this->id = null;
        }
    }

    public function selectApi($file) {
        $sql = "SELECT * FROM api WHERE filename = '" . $file . "' AND created_at = ( SELECT MAX(created_at) FROM api WHERE filename = '". $file . "')";
        $smtp = $this->pdo->executeStatement($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) == 1) {
                return $record;
            }
        } else {
            return 0;
        }
    }

    public function insert($file) {
        $this->date = date('Y-m-d H:i:s', strtotime('now'));
        $smtp = 'INSERT INTO api ( filename, created_at) VALUES ("' . $file .'" , DATE_FORMAT("' . $this->date . '", "%Y-%m-%d %H:%i:%s"))';
        $this->pdo->executeStatement($smtp);
        $this->selectApiId($file);
    }
}


