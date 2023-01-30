<?php

namespace App\Classes\ProductPrice;

use DB;

Class productPriceClassOBJ {

    public $productPrice;
    public $product;
    public $quantity;
    public $quantityUnit;
    public $currency;
    public $customer;

    function __construct($product, $quantity, $quantityUnit, $currency) {
        $this->productPrice = 0;
        $this->priceArray = [];
        $this->product = $product;
        $this->quantity = $quantity;
        $this->quantityUnit = $quantityUnit;
        $this->currency = $currency;
        $this->customer = !empty(myUser::user()->customerId) ? myUser::user()->customerId : 3;
    }

    public function getProductPrice() {

        $productPrice = $this->getLastProductPrice();
        if (!is_null($productPrice)) {
            $util = ['melyik' => 'lastProductPrice', 'ar' => floatval($productPrice->Price), 'offerOverride' => 0];
            array_push( $this->priceArray, $util);
        }

        $contractPrice = $this->getContractPrice();
        if (count($contractPrice) > 0) {
            for ($i = 0; $i < count($contractPrice); $i++) {
                $util = ['melyik' => 'contractPrice', 'ar' => floatval($contractPrice[$i]->Price), 'offerOverride' => $contractPrice[$i]->OfferOverride];
                array_push($this->priceArray, $util);
            }
        }

        $offerPrice = $this->getOfferPrice();
        if (!is_null($offerPrice)) {
            $util = ['melyik' => 'offerPrice', 'ar' => floatval($offerPrice->SalesPrice), 'offerOverride' => 0];
            array_push($this->priceArray, $util);
        }

        return $this->priceBack();
    }

    public function getLastProductPrice() {
        $productPrice = DB::table('ProductPrice')
            ->where('PriceCategory', function ($query) {
                return $query->from('Customer')->select('PriceCategory')->where('Id', $this->customer)->first();
            })
            ->where('Product', $this->product)
            ->where('QuantityUnit', $this->quantityUnit)
            ->where('Currency', $this->currency)
            ->where('ValidFrom', '<=', \Carbon\Carbon::now())
            ->orderBy('ValidFrom', 'desc')
            ->first();

        return $productPrice;

    }

    public function getContractPrice() {
        $contractPrice = DB::table('CustomerContract as t1')
            ->join('CustomerContractDetail as t2', 't2.CustomerContract', '=', 't1.Id')
            ->select('t1.*', 't2.Product', 't2.Price', 't2.Currency', 't2.ValidFrom as vf', 't2.ValidTo as vt')
            ->where('t2.ValidFrom', '<=', \Carbon\Carbon::now())
            ->where(function($query) {
                $query->where('t2.ValidTo','>=', \Carbon\Carbon::now())
                    ->orWhereNull('t2.ValidTo');
            })
            ->where('t1.Customer', $this->customer)
            ->where('t2.Product', $this->product)
            ->where('t2.QuantityUnit', $this->quantityUnit)
            ->where('t2.Currency', $this->currency)
            ->get();

        return $contractPrice;
    }

    public function getOfferPrice() {
        $offerPrice = DB::table('CustomerOfferDetail as t1')
            ->join('CustomerOffer as t2', 't2.Id', '=', 't1.CustomerOffer')
            ->join('CustomerOfferCustomer as t3', 't3.CustomerOffer', '=', 't2.Id')
            ->select('t1.*')
            ->where('t2.ValidFrom', '<=', \Carbon\Carbon::now())
            ->where('t2.ValidTo', '>=', \Carbon\Carbon::now())
            ->where('t3.Forbid', 0)
            ->where('t1.Currency', $this->currency)
            ->where('t1.QuantityUnit', $this->quantityUnit)
            ->where('t1.Product', $this->product)
            ->where(function ($query) {
                $query->where('t3.Customer', $this->customer)
                    ->orWhere('t3.CustomerCategory', function ($query) {
                        $query->from('Customer')->select('CustomerCategory')->where('Id', $this->customer)->first();
                    });
            })
            ->where(function ($query) {
                $query->where('QuantityMinimum', '<=', $this->quantity)
                    ->orWhereNull('QuantityMinimum');
            })
            ->orderByDesc('t1.QuantityMinimum')
            ->orderByDesc('t1.QuantityMaximum')
            ->first();

        return $offerPrice;

    }

    public function priceBack()
    {
        $key = array_search('contractPrice', array_column($this->priceArray, 'melyik'));
        if ($key) {
            $price = $this->priceArray[$key]['ar'];
            $offerOverride = $this->priceArray[$key]['offerOverride'];
            if ($offerOverride == 1) {
                $opKey = array_search('offerPrice', array_column($this->priceArray, 'melyik'));
                if ($opKey) {
                    $opPrice = $this->priceArray[$opKey]['ar'];
                    if ($opPrice < $price ) {
                        $price = $opPrice;
                    }
                }
            }
            return $price;
        } else {
            if (count($this->priceArray) > 0) {
                $price = $this->priceArray[0]['ar'];
                for ($i = 1; $i < count($this->priceArray); $i++) {
                    if ($price > $this->priceArray[$i]['ar']) {
                        $price = $this->priceArray[$i]['ar'];
                    }
                }
                return $price;
            }
        }
    }

}
