<?php

namespace App\Traits\CustomerContactFavoriteProduct;

use Illuminate\Http\Request;
use myUser;
use DB;
use Yajra\DataTables\Facades\DataTables;

trait ProductCategoryProductIndex {

    public function productCategoryProductIndex(Request $request, $category)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = DB::table('product')
                    ->where(function($query) use ($category) {
                        if (is_null($category) || $category == -999999) {
                            $query->whereNotNull('ProductCategory');
                        } else {
                            $query->where('ProductCategory', $category);
                        }
                    })
                    ->whereNotIn('Id', function ($query) {
                        return $query->from('customercontactfavoriteproduct')
                            ->select('product_id')
                            ->where('customercontact_id', myUser::user()->customercontact_id)
                            ->get();
                    })
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);

            }

            return back();
        }
    }

}
