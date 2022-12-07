<?php
namespace App\Classes;

use DB;
use App\Models\Users;
use App\Models\Customer;
use myUser;

Class myUserClass{

    /**
     * bejelentkezett USER adatai
     *
     * @return firebird->Employee
     */
    public static function user()
    {
        return Users::where('id', session('user_id'))->first();
    }

    /**
     * van-e bejentkezett user
     *
     * @return bool
     */
    public static function check()
    {
        $employee = Db::table('users')->where('id', session('user_id'))->first();
        return !empty($employee) ? true : false;

    }

    /**
     * Bejelentkezett felhasználó rendszergazdai státusza
     */
    public static function rendszergazdaiStatusz()
    {
        return Db::table('users')->where('id', session('user_id'))->first()->rendszergazda;
    }

    /**
     * Bejelentkezett felhasználó cégének neve
     */
    public static function customerName()
    {
        return Customer::where('Id', function ($query) {
            $query->from('customercontact')->select('Customer')->where('Id', function ($query) {
                $query->from('users')->select('customercontact_id')->where('id', myUser::user()->id)->first();
            })->first();
        } )->first()->Name;
    }

    /**
     * Bejelentkezett felhasználó cégének id-a
     */
    public static function customerId()
    {
        return Customer::where('Id', function ($query) {
            $query->from('customercontact')->select('Customer')->where('Id', function ($query) {
                $query->from('users')->select('customercontact_id')->where('id', myUser::user()->id)->first();
            })->first();
        } )->first()->Id;
    }

}
