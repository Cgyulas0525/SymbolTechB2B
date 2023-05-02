<?php

namespace App\Services;

use DB;
use myUser;
use DataTables;

class CustomerOrderService
{
    public function allData()
    {

        return DB::table('customerorder as t1')
            ->selectRaw('t1.Id, t1.VoucherNumber, t1.VoucherDate, t1.NetValue, t1.VatValue, t1.GrossValue , t3.Name as currencyName, SUM(1) as DetailNumber, t4.Name as statusName', )
            ->join('customerorderdetail as t2', 't2.CustomerOrder', '=', 't1.Id')
            ->join('currency as t3', 't3.Id', '=', 't1.Currency')
            ->leftJoin('customerorderstatus as t4', 't4.Id', '=', 't1.CustomerOrderStatus')
            ->where('t1.Customer', myUser::user()->customerId)
            ->groupBy('t1.Id', 't1.VoucherNumber', 't1.VoucherDate', 't1.NetValue', 't1.VatValue', 't1.GrossValue', 't3.Name', 't4.Name')
            ->get();
    }

    public function dwData($data, $sc = null) {

        $func = is_null($sc) ? 'customerOrders.edit' : 'editSc';

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('tetelszam', function($data) { return $data->DetailNumber; })
            ->addColumn('currencyName', function($data) { return $data->currencyName; })
            ->addColumn('statusName', function($data) { return $data->statusName; })
            ->addColumn('action', function($row) use ($func) {
                $btn = '';
                if ($row->DetailNumber > 0 && $row->NetValue > 0) {
                    $btn = '<a href="' . route($func, [$row->Id]) . '"
                                 class="edit btn btn-success btn-sm editProduct" title="Tételek"><i class="far fa-list-alt"></i></a>';

                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function allShoppingCart()
    {

        // TODO itt is kérdés, hogy mindet vagy csak a saját kosarakat láthatja-e?

        return DB::table('shoppingcart as t1')
            ->selectRaw('t1.Id, t1.VoucherNumber, t1.VoucherDate, t1.NetValue, t1.VatValue, t1.GrossValue , t3.Name as currencyName, SUM(1) as DetailNumber, "" as statusName')
            ->join('shoppingcartdetail as t2', 't2.ShoppingCart', '=', 't1.Id')
            ->join('currency as t3', 't3.Id', '=', 't1.Currency')
            ->where('t1.Customer', myUser::user()->customerId)
            ->where('t1.CustomerContact', myUser::user()->customercontact_id)
            ->where('t1.Opened', 1)
            ->groupBy('t1.Id', 't1.VoucherNumber', 't1.VoucherDate', 't1.NetValue', 't1.VatValue', 't1.GrossValue', 't3.Name')
            ->get();
    }

}
