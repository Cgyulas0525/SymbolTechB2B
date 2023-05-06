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

                $data = ShoppingCartDetail::with('shoppingCartRelation')->where('ShoppingCart', $id)->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('CurrencyName', function($data) { return $data->CurrencyName; })
                    ->addColumn('ProductName', function($data) { return $data->ProductName; })
                    ->addColumn('QuantityUnitName', function($data) { return $data->QuantityUnitName; })
                    ->addColumn('VatRate', function($data) { return $data->VatRate; })
                    ->addColumn('action', function($row){
                        $btn = '';
                        if ( $row->shoppingCartRelation->Opened === 0 )
                            $btn = $btn.'<a href="' . route('beforeSCDDestroy', [$row->Id]) . '"
                                     class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="fa fa-trash"></i></a>';
                        else {
                            $btn = $btn.'<a href="' . route('beforeSCDCopy', [$row->Id]) . '"
                                     class="btn btn-primary btn-sm deleteProduct" title="Másolás"><i class="fas fa-clone"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);

            }

            return view('shopping_carts.index');
        }
    }

}
