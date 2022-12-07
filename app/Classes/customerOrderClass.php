<?php
namespace App\Classes;

use App\Models\CustomerOrderDetail;
use DB;
use App\Models\CustomerOrder;

Class customerOrderClass{

    public static function nyitottMegrendelesek($customer)
    {
        return CustomerOrder::where('Customer', $customer)->get()->count();
    }

    public static function nyitottMegrendelesTetelSzam($customer)
    {
        return CustomerOrderDetail::whereIn('CustomerOrder', function ($query) use($customer) {
            return $query->select('Id')->from('customerorder')->where('Customer', $customer)->get();
        })->get()->count();
    }

    public static function openCustomerOrderValue($customer)
    {
        $ertek = CustomerOrderDetail::selectRaw('sum(Quantity * UnitPrice) as ertek')
            ->whereIn('CustomerOrder', function ($query) use($customer) {
                return $query->select('Id')->from('customerorder')->where('Customer', $customer)->get();
            })->get();
        return !empty($ertek[0]->ertek) ? $ertek[0]->ertek : 0;
    }
}
