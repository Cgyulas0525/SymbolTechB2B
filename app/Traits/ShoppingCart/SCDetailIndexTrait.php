<?php

namespace App\Traits\ShoppingCart;

use App\Models\ShoppingCartDetail;
use Illuminate\Http\Request;
use DataTables;
use myUser;

trait SCDetailIndexTrait {
    public function shoppingCartDetailIndex(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = ShoppingCartDetail::where('ShoppingCart', $id)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('CurrencyName', function($data) { return $data->CurrencyName; })
                    ->addColumn('ProductName', function($data) { return $data->ProductName; })
                    ->addColumn('QuantityUnitName', function($data) { return $data->QuantityUnitName; })
                    ->addColumn('VatRate', function($data) { return $data->VatRate; })
                    ->addColumn('action', function($row){
                        $btn = '<a href="' . route('shoppingCartDetailDestroy', [$row->Id]) . '"
                                 class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);

            }

            return view('shopping_carts.index');
        }
    }

}
