<?php

namespace App\Traits\ShoppingCart;

use App\Actions\ShoppingCart\dwRawAction;
use Illuminate\Http\Request;
use DataTables;
use DB;
use myUser;

trait FavoriteProductIndexTrait {

    public function favoriteProductIndex(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $dbRaw = new dwRawAction();

                $data = DB::table('Product as t1')
                    ->join('ProductPrice as t2', 't2.Product', '=', 't1.Id')
                    ->join('customercontactfavoriteproduct as t5', function($join) {
                        $join->on('t1.Id', '=', 't5.product_id')
                            ->where('t5.customercontact_id', myUser::user()->customercontact_id);
                    })
                    ->leftJoin('ProductCategory as t3', 't3.Id', '=', 't1.ProductCategory' )
                    ->leftJoin('QuantityUnit as t4', 't4.Id', '=', 't1.QuantityUnit')
                    ->select('t1.Id', 't1.Code', 't1.Barcode', 't1.Name as ProductName', 't3.Name as ProductCategoryName',
                        't4.Name as QuantityUnitName', $dbRaw->handle('t1.Id'))
                    ->where('t1.Inactive', 0)
                    ->where('t1.Service', 0)
                    ->where('t1.Deleted', 0)
                    ->where('t2.PriceCategory', 2)
                    ->where('t2.Currency', -1)
                    ->groupBy('t1.Id', 't1.Code', 't1.Barcode', 't1.Name', 't3.Name', 't4.Name')
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Quantity', function($data) { return 0; })
                    ->make(true);

            }

            return view('shopping_cart_details.index');
        }

    }

}
