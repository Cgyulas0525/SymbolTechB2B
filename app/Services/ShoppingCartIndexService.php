<?php

namespace App\Services;

use DB;
use myUser;
use DataTables;

class ShoppingCartIndexService
{

    public function allData($customerContact, $year) {

        return DB::table('ShoppingCart as t1')
            ->join('ShoppingCartDetail as t2', 't2.ShoppingCart', '=', 't1.Id')
            ->join('Currency as t3', 't3.Id', '=', 't1.Currency')
            ->join('PaymentMethod as t4', 't4.Id', '=', 't1.PaymentMethod')
            ->join('TransportMode as t5', 't5.Id', '=', 't1.TransportMode')
            ->leftJoin('CustomerOrder as t6', 't6.Id', '=', 't1.CustomerOrder')
            ->select(DB::raw('t1.Id, t1.VoucherNumber, t1.VoucherDate, t1.DeliveryDate, t1.NetValue, t1.GrossValue, t1.VatValue, t1.Opened,
                                  t3.Name as CurrencyName, t4.Name as PaymentMethodName, t5.Name as TransportModeName, t6.VoucherNumber as CustomerOrderVoucherNumber,
                                  sum(1) as DetailNumber'))
            ->where('t1.Customer', myUser::user()->customerId)
            ->where( function($query) use ($customerContact) {
                if (!is_null($customerContact) && $customerContact != -99999) {
                    $query->where('t1.CustomerContact', $customerContact);
                }
            })
            ->where( function($query) use ($year) {
                if (!is_null($year)) {
                    $query->whereYear('t1.VoucherDate', $year);
                }
            })
            ->whereNull('t1.deleted_at')
            ->whereNull('t2.deleted_at')
            ->groupBy('t1.Id', 't1.VoucherNumber', 't1.VoucherDate', 't1.DeliveryDate', 't1.NetValue', 't1.GrossValue', 't1.VatValue', 't1.Opened',
                'CurrencyName', 'PaymentMethodName', 'TransportModeName', 'CustomerOrderVoucherNumber')
            ->get();
    }

    public function dwData($data) {

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';
                if ($row->Opened == 0) {
                    $btn = '<a href="' . route('shoppingCarts.edit', [$row->Id]) . '"
                         class="edit btn btn-success btn-sm editProduct" title="Módosítás"><i class="fa fa-paint-brush"></i></a>';
                    $btn = $btn.'<a href="' . route('shoppingCarts.destroy', [$row->Id]) . '"
                         class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="fa fa-trash"></i></a>';
                    $btn = $btn.'<a href="' . route('shoppingCartDetailCreate', [$row->Id]) . '"
                         class="btn btn-warning btn-sm deleteProduct" title="Tételek"><i class="far fa-list-alt"></i></a>';
                    $btn = $btn.'<a href="' . route('shoppingCartClose', [$row->Id]) . '"
                         class="btn btn-primary btn-sm deleteProduct" title="Zárás"><i class="fas fa-store-slash"></i></a>';
                }
                if ($row->Opened == 1) {
                    $btn = '<a href="' . route('shoppingCarts.edit', [$row->Id]) . '"
                         class="edit btn btn-success btn-sm editProduct" title="Módosítás"><i class="fa fa-paint-brush"></i></a>';

                    $btn = $btn.'<a href="' . route('beforeAllSCDCopy', [$row->Id]) . '"
                                     class="btn btn-warning btn-sm deleteProduct" title="Másolás"><i class="fas fa-clone"></i></a>';

                    $btn = $btn.'<a href="' . route('shoppingCartOpen', [$row->Id]) . '"
                         class="btn btn-primary btn-sm deleteProduct" title="Nyítás"><i class="fas fa-store-alt"></i></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);

    }
}
