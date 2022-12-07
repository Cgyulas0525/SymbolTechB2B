<?php

class apimodel
{
    public $id;
    public $api_id;
    public $model;
    public $recordnumber;
    public $insertednumber;
    public $updatednumber;
    public $errornumber;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    function __construct() {
        $this->pdo = new mySQLDatabase();
        $this->date = date('Y-m-d H:i:s', strtotime('now'));
    }

    public function selectId() {
        $sql = "SELECT id FROM apimodel WHERE api_id = " . $this->api_id . " AND model = '" . $this->model . "'
               AND created_at = ( SELECT MAX(created_at) FROM apimodel WHERE api_id = ". $this->api_id . " AND model = '" .$this->model. "')";
        $smtp = $this->pdo->executeStatement($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) == 1) {
                foreach ($record as $row) {
                    $id = $row['id'];
                }
                return $id;
            }
        } else {
            return null;
        }
    }

    public function updateErrornumber() {
        $sql = "UPDATE apimodel SET errornumber = " . $this->errornumber . ", insertednumber = " . $this->insertednumber . ",
                                    updatednumber = " . $this->updatednumber . ", recordnumber = " . $this->recordnumber . "
                WHERE api_id = " . $this->api_id . " AND model = '" . $this->model . "'";
        $this->pdo->executeStatement($sql);
    }

    public function insert() {
        $this->date = date('Y-m-d H:i:s', strtotime('now'));
        $smtp = 'INSERT INTO apimodel ( api_id, model, recordnumber, errornumber, created_at)
                 VALUES (' . $this->api_id . ',"'
                           . $this->model . '",'
                           . $this->recordnumber . ','
                           . $this->errornumber .
                           ', DATE_FORMAT("' . $this->date . '", "%Y-%m-%d %H:%i:%s"))';
        $this->pdo->executeStatement($smtp);
        $this->id = $this->selectId();
    }

}
