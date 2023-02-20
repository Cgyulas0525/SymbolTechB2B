<?php

class productpricemodel {

    public $Id;
    public $Product;
    public $Currency;
    public $Validfrom;
    public $PriceCategory;
    public $QuantityUnit;
    public $Price;
    public $RowCreate;
    public $RowModify;

    function setId($Id) {
        $this->Id = $Id;
    }
    function setProduct($Product) {
        $this->Product = $Product;
    }
    function setCurrency($Currency) {
        $this->Currency = $Currency;
    }
    function setValidfrom($Validfrom) {
        $this->Validfrom = $Validfrom;
    }
    function setPriceCategory($PriceCategory) {
        $this->PriceCategory = $PriceCategory;
    }
    function setQuantityUnit($QuantityUnit) {
        $this->QuantityUnit = $QuantityUnit;
    }
    function setPrice($Price) {
        $this->Price = $Price;
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
    function getCurrency() {
        return $this->Currency;
    }
    function getValidfrom() {
        return $this->Validfrom;
    }
    function getPriceCategory() {
        return $this->PriceCategory;
    }
    function getQuantityUnit() {
        return $this->QuantityUnit;
    }
    function getPrice() {
        return $this->Price;
    }
    function getRowCreate() {
        return $this->RowCreate;
    }
    function getRowModify() {
        return $this->RowModify;
    }

}
