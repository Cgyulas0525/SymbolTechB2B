<?php
namespace App\Classes;

use DB;
use App\Models\Employee;
use App\Models\Dictionaries;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\TransportMode;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Models\ProductCategory;
use App\Models\Product;

use shoppingCartClass;
use App\Classes\Customer\paymentMethodClass;

Class ddwClass{

    public static function excelImportDDW()
    {
        $array = [];
        return $array;
    }

    public static function logEventDDW() {
        return ["Login", "Logout", "Copy", "Finalize order", 'Insert', 'Modify', 'Delete'];
    }

    public static function logEvent($value) {
        if ( $value === 1 ) {
            return "Login";
        } elseif ( $value === 2 ) {
            return "Logout";
        } elseif ( $value === 3 ) {
            return "Copy";
        } elseif ( $value === 4 ) {
            return "Finalize order";
        } elseif ( $value === 5 ) {
            return "Insert";
        } elseif ( $value === 6 ) {
            return "Modify";
        } elseif ( $value === 7 ) {
            return "Delete";
        }
    }

    public static function employeeNotB2BDDW() {
        return [" "] + Employee::whereNotIn('Id', function($query) {
            return $query->from('users')->select('employee_id')->whereNotNull('employee_id')->whereNull('deleted_at')->get();
        })->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function dictionaryDDW($tipus) {
        return [" "] + Dictionaries::where('tipus', '=', $tipus)->orderBy('nev')->pluck('nev', 'id')->toArray();
    }

    public static function belsoStatuszDDW() {
        return [" "] + Dictionaries::where('tipus', '=', 1)->where('id', '>', 1)->orderBy('nev')->pluck('nev', 'id')->toArray();
    }

    public static function customerDDW() {
        return [" "] + DB::table('CustomerContact as t1')
            ->join('Customer as t2', 't2.Id', "=", 't1.Customer' )
            ->select('t2.Name', 't2.Id', DB::raw('sum(1) as db'))
            ->where('t2.Deleted', 0)->where('t2.IsCompany', 1)->whereNull('t2.WebUserName')
            ->groupBy('t2.Name')
            ->groupBy('t2.Id')
            ->orderBy('t2.Name')->pluck('Name', 'Id')->toArray();
    }

    public static function customerContactDDW($customer = null) {
        return [" "] + CustomerContact::where('Deleted', 0)->where('Customer', $customer)->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function logItemCustomerDDw()
    {
        return [" "] + DB::table('logitem as t1')
                         ->leftJoin('customer as t2', 't2.Id', '=', 't1.customer_id')
                         ->selectRaw('if(t2.Name IS NULL, "'. session('customer_name') . '", t2.Name) as customerName, t1.customer_id as customer_id, sum(1) as db')
                         ->groupBy('customerName', 't1.customer_id')
                         ->pluck('customerName', 't1.customer_id')
                         ->toArray();
    }

    public static function logItemUserDDW($customer) {
        return [" "] + DB::table('logitem as t1')
                ->join('users as t2', 't2.id', '=', 't1.user_id' )
                ->selectRaw('t2.name as Name, t1.user_id as Id, sum(1) as db')
                ->where('t1.customer_id', $customer)
                ->groupBy('Name', 'Id')
                ->pluck('Name', 'Id')
                ->toArray();
    }

    public static function indexData($startDate, $endDate, $customer, $user)
    {

        return DB::table('logitem as t1')
            ->leftJoin('customer as t2', 't2.Id', '=', 't1.customer_id')
            ->join('users as t3', 't3.id', '=', 't1.user_id')
            ->select('t1.*', 't2.Name as customerName', 't3.name as userName')
            ->whereBetween('t1.eventdatetime', [ $startDate, $endDate])
            ->where( function($query) use ($customer) {
                if (is_null($customer)) {
                    $query->whereNotNull('t1.customer_id');
                } else {
                    $query->where('t1.customer_id', '=', $customer);
                }
            })
            ->where( function($query) use ($user) {
                if (is_null($user)) {
                    $query->whereNotNull('t1.user_id');
                } else {
                    $query->where('t1.user_id', '=', $user);
                }
            })
            ->get();
    }

    public static function transportmodeDDW()
    {
        return TransportMode::where('Deleted', 0)->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function paymentmethodDDW()
    {
        return PaymentMethod::where('Deleted', 0)->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function currencyDDW()
    {
        return Currency::where('Deleted', 0)->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function customerAddressDDW($customer = null)
    {
        return [" "] + DB::table('customeraddress')
            ->selectRaw('Concat(Name, " - " , Country, " ", Zip, ". ", City, " ", Street, " ", HouseNumber) as cim, Id' )
            ->where('Customer', $customer)
            ->where('Deleted', 0)
            ->orderBy('cim')
            ->pluck('cim', 'Id')
            ->toArray();
    }

    public static function customerPaymentMethodDDW()
    {
        return PaymentMethod::where('UseAlways', 1)
            ->orWhere('Id', paymentMethodClass::customerPaymentMethod())
            ->where('Deleted', 0)->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function productDDW()
    {
        return DB::table('product')
                 ->select('Name', 'Id')
                 ->where('Inactive', 0)
                 ->orderBy('Name')->pluck('Name', 'Id')->toArray();
//        return Product::where('Inactive', 0)->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function productCategoryDDW()
    {
        return [" "] + DB::table('productcategory')->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }

    public static function productProductCategoryDDW()
    {
        return [" "] + DB::table('productcategory as t1')
            ->select("t1.Id", "t1.Name")
            ->whereIn("t1.Id", function($query) {
                $query->from("product as t2")->select("t2.ProductCategory")->distinct()->get();
            })->orderBy('Name')->pluck('Name', 'Id')->toArray();
    }



}
