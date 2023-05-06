<?php
namespace App\Classes;

use App\Classes\ProductPrice\productPriceClassOBJ;

Class productPriceClass {

    /*
     * current product price
     *
     * @param $product
     * @param $quantity
     * @param quantityUnit
     * @param currency
     *
     * @return price: float
     */
    public static function getProductPrice($product, $quantity, $quantityUnit, $currency) {

        $ppc = new productPriceClassOBJ($product, $quantity, $quantityUnit, $currency);

        return $ppc->getProductPrice();

    }
}
