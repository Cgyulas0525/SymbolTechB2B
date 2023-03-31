<?php

namespace App\Traits\ShoppingCart;

use App\Actions\ShoppingCart\dwRawAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use DB;
use myUser;

trait CustomerContractProductIndexTrait {
    public function customerContractProductIndex (Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $dbRaw = new dwRawAction();

                $item1 = DB::table('CustomerContract as t1')
                    ->join( 'CustomerContractDetail as t2', 't2.CustomerContract', '=', 't1.Id')
                    ->join('Product as t3', 't3.Id', '=', 't2.Product')
                    ->leftJoin('ProductCategory as t4', 't4.Id', '=', 't3.ProductCategory' )
                    ->leftJoin('QuantityUnit as t5', 't5.Id', '=', 't3.QuantityUnit')
                    ->select('t3.Id', 't3.Code', 't3.Barcode', 't3.Name as ProductName', 't4.Name as ProductCategoryName',
                        't5.Name as QuantityUnitName', $dbRaw->handle('t3.Id'))
                    ->where('t1.ValidFrom', '<=' , Carbon::parse(now()))
                    ->where('t1.Customer', myUser::user()->customer_id)
                    ->whereNull('t1.ValidTo');

                $item2 = DB::table('CustomerContract as t1')
                    ->join( 'CustomerContractDetail as t2', 't2.CustomerContract', '=', 't1.Id')
                    ->join('Product as t3', 't3.Id', '=', 't2.Product')
                    ->leftJoin('ProductCategory as t4', 't4.Id', '=', 't3.ProductCategory' )
                    ->leftJoin('QuantityUnit as t5', 't5.Id', '=', 't3.QuantityUnit')
                    ->select('t3.Id', 't3.Code', 't3.Barcode', 't3.Name as ProductName', 't4.Name as ProductCategoryName',
                        't5.Name as QuantityUnitName', $dbRaw->handle('t3.Id'))
                    ->where('t1.ValidFrom', '<=' , Carbon::parse(now()))
                    ->where('t1.Customer', myUser::user()->customer_id)
                    ->where('t1.ValidTo', '>=', Carbon::parse(now()))
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
