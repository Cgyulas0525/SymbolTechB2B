<?php

namespace App\Traits\ShoppingCart;

use App\Actions\ShoppingCart\dwRawAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use DB;
use myUser;

trait CustomerOfferProductIndexTrait {
    public function customerOfferProductIndex (Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $dbRaw = new dwRawAction();

                $item1 = DB::table('CustomerOffer as t1')
                    ->join('CustomerOfferCustomer as t2', 't2.CustomerOffer', '=', 't1.Id')
                    ->join('CustomerOfferDetail as t3', 't3.CustomerOffer', '=', 't1.Id')
                    ->join('Product as t4', 't4.Id', '=', 't3.Product')
                    ->leftJoin('ProductCategory as t5', 't5.Id', '=', 't4.ProductCategory' )
                    ->leftJoin('QuantityUnit as t6', 't6.Id', '=', 't4.QuantityUnit')
                    ->select('t4.Id', 't4.Code', 't4.Barcode', 't4.Name as ProductName', 't5.Name as ProductCategoryName',
                        't6.Name as QuantityUnitName', $dbRaw->handle('t4.Id'))
                    ->where('t2.Customer', myUser::user()->customer_id)
                    ->where( 't1.ValidFrom', '<=', Carbon::parse(now()))
                    ->where( 't1.ValidTo', '>=', Carbon::parse(now()))
                    ->groupBy('t4.Id');

                $item2 = DB::table('CustomerOffer as t1')
                    ->join('CustomerOfferCustomer as t2', 't2.CustomerOffer', '=', 't1.Id')
                    ->join('CustomerOfferDetail as t3', 't3.CustomerOffer', '=', 't1.Id')
                    ->join('Product as t4', 't4.Id', '=', 't3.Product')
                    ->leftJoin('ProductCategory as t5', 't5.Id', '=', 't4.ProductCategory' )
                    ->leftJoin('QuantityUnit as t6', 't6.Id', '=', 't4.QuantityUnit')
                    ->join('CustomerCategory as t7', 't7.Id', '=', 't2.CustomerCategory')
                    ->join('Customer as t8', 't8.CustomerCategory', '=', 't7.Id')
                    ->select('t4.Id', 't4.Code', 't4.Barcode', 't4.Name as ProductName', 't5.Name as ProductCategoryName',
                        't6.Name as QuantityUnitName', $dbRaw->handle('t4.Id'))
                    ->whereNotNull('t2.CustomerCategory')
                    ->where('t8.Id', myUser::user()->customer_id)
                    ->where( 't1.ValidFrom', '<=', Carbon::parse(now()))
                    ->where( 't1.ValidTo', '>=', Carbon::parse(now()))
                    ->groupBy('t4.Id')
                    ->union($item1);


                $data = DB::query()->fromSub($item2, 'item')->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Quantity', function($data) { return 0; })
                    ->make(true);

            }

            return view('shopping_cart_details.index');
        }

    }

}
