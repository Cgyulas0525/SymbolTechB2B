<?php
namespace App\Classes;

use App\Models\CustomerOrderDetail;
use DB;
use App\Models\CustomerOrder;

Class customerOrderClass{

    public static function openCustomerOrderDetailCount($customer)
    {
        return CustomerOrderDetail::whereIn('CustomerOrder', function ($query) use($customer) {
            return $query->select('Id')->from('customerorder')->where('Closed', 0)->where('Customer', $customer)->get();
        })->get()->count();
    }

    public static function openCustomerOrderValue($customer)
    {
        $value = CustomerOrderDetail::selectRaw('sum(Quantity * UnitPrice) as value')
            ->whereIn('CustomerOrder', function ($query) use($customer) {
                return $query->select('Id')->from('customerorder')->where('Closed', 0)->where('Customer', $customer)->get();
            })->get();
        return !empty($value->first()->value) ? $value->first()->value : 0;
    }

    public static function openContactCustomerOrderDetailCount($customer, $contact)
    {
        return CustomerOrderDetail::whereIn('CustomerOrder', function ($query) use($customer, $contact) {
            return $query->select('Id')
                         ->from('customerorder')
                         ->where('Closed', 0)
                         ->where('Customer', $customer)
                         ->where('CustomerContact', $contact)
                         ->get();
        })->get()->count();
    }

    public static function openContactCustomerOrderValue($customer, $contact)
    {
        $value = CustomerOrderDetail::selectRaw('sum(Quantity * UnitPrice) as value')
            ->whereIn('CustomerOrder', function ($query) use($customer, $contact) {
                return $query->select('Id')
                            ->from('customerorder')
                            ->where('Closed', 0)
                            ->where('Customer', $customer)
                            ->where('CustomerContact', $contact)
                            ->get();
            })->get();
        return !empty($value->first()->value) ? $value->first()->value : 0;
    }

}
