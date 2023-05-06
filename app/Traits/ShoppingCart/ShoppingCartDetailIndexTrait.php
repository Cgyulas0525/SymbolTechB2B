<?php

namespace App\Traits\ShoppingCart;

use App\Models\ShoppingCartDetail;
use Illuminate\Http\Request;
use DataTables;
use myUser;

trait ShoppingCartDetailIndexTrait {

    public function shoppingCartDetailIndex(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = ShoppingCartDetail::where('ShoppingCart', $id)->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<a href="#"
                             class="edit btn btn-success btn-sm editProduct" title="Módosítás"><i class="fa fa-paint-brush"></i></a>';
                        $btn = $btn.'<a href="#"
                             class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);

            }

            return view('shopping_cart_details.index');
        }
    }

}
