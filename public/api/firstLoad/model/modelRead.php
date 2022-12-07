<?php

define("MODELS_PATH", __DIR__ . "/../../../../App/Models/");


class modelRead {

    public $modelArray = [];
    public $castsArray = [];

    public function modelToArray($modelName) {
        $fileName = MODELS_PATH . $modelName;
        if (file_exists($fileName)) {
            $current = file($fileName);
            return array_values($current);
        } else {
            die('Nincs ilyen model: ' . $modelName);
        }
    }

    public function castField($value) {
        $spos = strpos($value, "'");
        $vmi = substr($value, $spos + 1);
        $epos = strpos($vmi, "'");
        return substr($value, $spos + 1, $epos);
    }

    public function castType($value) {
        $npos = strpos($value, "=>");
        $vmi = substr($value, $npos + 4);
        $epos = strpos($vmi, "'");
        return substr($vmi, 0, $epos);
    }

    public function castArray($modelName) {
        $this->castsArray = [];
        $this->modelArray = $this->modelToArray($modelName);
        $keys = array_keys($this->modelArray);
        $values = array_values($this->modelArray);
        $start = 0;
        $end   = 0;
        for ($i = 0; $i < count($keys); $i++) {
            if (strpos($values[$i], '$casts')) {
                $start = $i;
            }
            if ($start > 0 && $end == 0 && strpos($values[$i], ']' )) {
                $end = $i;
            }
        }
        for ($i = 0; $i < count($keys); $i++) {
            if ($start < $i && $i < $end) {
                array_push($this->castsArray, $values[$i]);
            }
        }
        return $this->castsArray;
    }

    public function typeSwitch($data, $value, $what) {
        $end = "";
        $field = $data[ $this->castField($value) != 'Foreignn' ? $this->castField($value) : 'Foreign'];
        if ($this->castField($value) == "Picture") {
            $field = null;
        }
        if (is_null($field)) {
            $end = 'NULL' . $what;
        } else {
            switch ($this->castType($value)) {
                case 'integer':
                    $end .= $field . $what;
                    break;
                case 'decimal:4':
                    $end .= $field . $what;
                    break;
                case 'string':
                    if (strpos($field, "'")) {
                        $field = str_replace( "'", "\"", $field);
                    }
                    $end .= '\'' . $field . '\'' . $what;
                    break;
                case 'datetime':
                    $end .= 'DATE_FORMAT("' . $field . '", "%Y-%m-%d %H:%i:%s")' . $what;
                    break;
                default:
                    $end .= $field . $what;
                    break;
            }
        }
        return $end;
    }

    public function makeInsert($data, $castsArray, $modelName) {
        $begin = 'INSERT INTO ' . $modelName . ' (';
        $end = ' VALUES (';
        for ( $i = 0; $i < count($castsArray); $i++ ) {
            if ($i < count($castsArray) -1) {
                $begin .= $this->castField($castsArray[$i]) . ',';
                $end .= $this->typeSwitch($data, $castsArray[$i], ',');
            } else {
                $begin .= $this->castField($castsArray[$i]) . ')';
                $end .= $this->typeSwitch($data, $castsArray[$i], ')');
            }
        }
        return $begin . $end;
    }

    public function makeUpdate($data, $castsArray, $modelName) {
        $begin = 'UPDATE ' . $modelName;
        $end = ' SET ';
        for ( $i = 0; $i < count($castsArray); $i++ ) {
            if ($i < count($castsArray) -1) {
                $end .= $this->castField($castsArray[$i]) . '=' . $this->typeSwitch($data, $castsArray[$i], ',');
            } else {
                $end .= $this->castField($castsArray[$i]) . '=' . $this->typeSwitch($data, $castsArray[$i], ' ');
            }
        }
        return $begin . $end . ' WHERE Id = ' . $data['Id'];
    }

    public function loader($datas, $modelName, $castArray, $mySQLPDO) {
        foreach ($datas as $data) {
            $countSQL = 'SELECT * FROM ' . $modelName . ' WHERE Id = ' . $data['Id'];
            $sql = '';
            if (count($mySQLPDO->select($countSQL)) == 0) {
                $sql = $this->makeInsert($data, $castArray, $modelName);
            } else {
                $sql = $this->makeUpdate($data, $castArray, $modelName);
            }
            $mySQLPDO->select($sql);
        }
    }
}
