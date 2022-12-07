<?php
namespace App\Classes;

use DB;

Class adminClass{

    /*
     * B2B customer customercontact count
     *
     * @return integer array
    */
    public static function B2BCustomerContactCount()
    {
        return DB::table('customercontact  as t1')
            ->join('users as t2', 't2.customercontact_id', '=', 't1.Id')
            ->join( 'customer as t3', 't3.Id', '=', 't1.Customer')
            ->selectRaw('t3.Name as customerName, sum(1) as db')
            ->where('t2.rendszergazda', 0)
            ->whereNull('t2.deleted_at')
            ->groupBy('customerName')
            ->get();
    }

    /*
     * B2B CustomerContact LogIn count
     *
     * $return integer array
     */
    public static function B2BCustomerContactLoginCount()
    {
        return DB::table('logitem as t1')
                 ->join('users as t2', 't2.id', '=', 't1.user_id')
                 ->selectRaw('t2.name as customerContactName, sum(1) as db')
                 ->whereNull('t2.deleted_at')
                 ->whereNotNull('t2.customercontact_id')
                 ->groupBy('customerContactName')
                 ->get();
    }

    /*
     * B2B CustomerContact LogIn
     *
     * $return integer array
     */
    public static function B2BCustomerContactLogin($tol, $ig)
    {
        return DB::table('logitem as t1')
            ->join('users as t2', 't2.id', '=', 't1.user_id')
            ->join('customercontact as t3', 't3.Id', '=', 't2.customercontact_id')
            ->join('customer as t4', 't4.Id', '=', 't3.Customer')
            ->select('t1.*', 't4.Name as customerName', 't2.name as customerContactName')
            ->whereNull('t2.deleted_at')
            ->whereNotNull('t2.customercontact_id')
            ->whereBetween('t1.eventdatetime',
                [ !empty($tol) ? $tol : date('Y-m-d H:i:s', strtotime('first day ot this year - 10 years')),
                    !empty($ig) ? $ig : date('Y-m-d H:i:s', strtotime('last day of this year'))])
            ->get();
    }

    /*
     * B2B CustomerContact LogIn
     *
     * $return integer array
     */
    public static function B2BEmployeeLogin($tol, $ig, $statusz)
    {
        return DB::table('logitem as t1')
            ->join('users as t2', 't2.id', '=', 't1.user_id')
            ->join('employee as t3', 't3.Id', '=', 't2.employee_id')
            ->select('t1.*', 't3.Name as employeeName')
            ->whereNull('t2.deleted_at')
            ->whereNotNull('t2.employee_id')
            ->whereBetween('t1.eventdatetime',
                [ !empty($tol) ? $tol : date('Y-m-d H:i:s', strtotime('first day ot this year - 10 years')),
                  !empty($ig) ? $ig : date('Y-m-d H:i:s', strtotime('last day of this year'))])
            ->whereIn('t2.rendszergazda', !empty($statusz) ? $statusz : [ 1, 2])
            ->get();
    }

    /*
     * B2B Employee LogIn count
     *
     * $return integer array
     */
    public static function B2BEmployeeLoginCount()
    {
        return DB::table('logitem as t1')
            ->join('users as t2', 't2.id', '=', 't1.user_id')
            ->selectRaw('t2.name as customerContactName, sum(1) as db')
            ->whereNull('t2.deleted_at')
            ->whereNotNull('t2.employee_id')
            ->groupBy('customerContactName')
            ->get();
    }

    /*
     * B2B LogIn
     *
     * $return integer array
     */
    public static function B2BLogin($tol, $ig, $statusz)
    {
        return DB::table('logitem as t1')
            ->join('users as t2', 't2.id', '=', 't1.user_id')
            ->select('t1.*', 't2.name as userName')
            ->whereNull('t2.deleted_at')
            ->whereBetween('t1.eventdatetime',
                [ !empty($tol) ? $tol : date('Y-m-d H:i:s', strtotime('first day ot this year - 10 years')),
                    !empty($ig) ? $ig : date('Y-m-d H:i:s', strtotime('last day of this year'))])
            ->whereIn('t2.rendszergazda', !empty($statusz) ? $statusz : [0, 1, 2])
            ->get();
    }

    public static function B2BCustomerLoginCount($tol, $ig)
    {
        return DB::table('logitem as t1')
                ->join('users as t2', 't2.id', '=', 't1.user_id')
                ->join('customercontact as t3', 't3.Id', '=', 't2.customercontact_id')
                ->join('customer as t4', 't4.Id', '=', 't3.Customer')
                ->selectRaw('t4.Name as customerName, sum(1) as db')
                ->whereNull('t2.deleted_at')
                ->whereNotNull('t2.customercontact_id')
                ->whereBetween('t1.eventdatetime',
                    [ !empty($tol) ? $tol : date('Y-m-d H:i:s', strtotime('first day ot this year - 10 years')),
                        !empty($ig) ? $ig : date('Y-m-d H:i:s', strtotime('last day of this year'))])
                ->groupBy('customerName')
                ->get();
    }

}
