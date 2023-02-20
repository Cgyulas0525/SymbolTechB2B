<?php

class warehousebalance {

    public $Id;
    public $Product;
    public $Warehouse;
    public $Balance;
    public $AllocatedBalance;
    public $RowCreate;
    public $RowModify;

    function setId($Id) {
        $this->Id = $Id;
    }
    function setProduct($Product) {
        $this->Product = $Product;
    }
    function setWarehouse($Warehouse) {
        $this->Warehouse = $Warehouse;
    }
    function setBalance($Balance) {
        $this->Balance = $Balance;
    }
    function setAllocatedBalance($AllocatedBalance) {
        $this->AllocatedBalance = $AllocatedBalance;
    }

    function setRowCreate($RowCreate) {
        $this->RowCreate = $RowCreate;
    }
    function setRowModify($RowModify) {
        $this->RowModify = $RowModify;
    }

    function getId() {
        return $this->Id;
    }
    function getProduct() {
        return $this->Product;
    }
    function getWarehouse() {
        return $this->Warehouse;
    }
    function getBalance() {
        return $this->Balance;
    }
    function getAllocatedBalance() {
        return $this->AllocatedBalance;
    }
    function getRowCreate() {
        return $this->RowCreate;
    }
    function getRowModify() {
        return $this->RowModify;
    }

}
