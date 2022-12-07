<?php
namespace App\Classes;

use DB;

Class dashboardClass{

    public static function CustomerOrderInterval($from, $to)
    {
        return DB::table('customerorder as t')
            ->select(DB::raw('concat(year(t.VoucherDate), if(CAST(month(t.VoucherDate) AS UNSIGNED) < 10, concat("0", month(t.VoucherDate)), month(t.VoucherDate))) nev, Sum(t.GrossValue) osszeg'))
            ->whereBetween('t.VoucherDate', [$from, $to])
            ->where('t.Cancelled', 0)
            ->groupBy('nev')
            ->orderBy('nev')
            ->get();
    }

    public static function CustomerOrderSumInterval($from, $to)
    {
        return DB::table('customerorder as t')
            ->select(DB::raw('concat(year(t.VoucherDate), if(CAST(month(t.VoucherDate) AS UNSIGNED) < 10, concat("0", month(t.VoucherDate)), month(t.VoucherDate))) nev, Sum(1) osszeg'))
            ->whereBetween('t.VoucherDate', [$from, $to])
            ->where('t.Cancelled', 0)
            ->groupBy('nev')
            ->orderBy('nev')
            ->get();
    }

    public static function CustomerOrderDetailSumInterval($from, $to)
    {
        return DB::table('customerorder as t')
            ->join('customerorderdetail as t1', 't1.CustomerOrder', '=', 't.Id')
            ->select(DB::raw('concat(year(t.VoucherDate), if(CAST(month(t.VoucherDate) AS UNSIGNED) < 10, concat("0", month(t.VoucherDate)), month(t.VoucherDate))) nev, Sum(1) osszeg'))
            ->whereBetween('t.VoucherDate', [$from, $to])
            ->where('t.Cancelled', 0)
            ->groupBy('nev')
            ->orderBy('nev')
            ->get();
    }

    public static function CustomerOrderAverageSumInterval($from, $to)
    {
        return DB::table('customerorder as t')
            ->select(DB::raw('concat(year(t.VoucherDate), if(CAST(month(t.VoucherDate) AS UNSIGNED) < 10, concat("0", month(t.VoucherDate)), month(t.VoucherDate))) nev, Sum(t.GrossValue) osszeg, Sum(1) darab'))
            ->whereBetween('t.VoucherDate', [$from, $to])
            ->where('t.Cancelled', 0)
            ->groupBy('nev')
            ->orderBy('nev')
            ->get();
    }
}
