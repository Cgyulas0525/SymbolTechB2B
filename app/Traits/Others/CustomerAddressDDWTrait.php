<?php

namespace App\Traits\Others;

use Illuminate\Http\Request;
use DB;

trait CustomerAddressDDWTrait {

    /*
     * Customer telephelyei DDW
     *
     * @param $request
     *
     * @return array
     */
    public function customerAddressDDW(Request $request) {
        return DB::table('CustomerAddress')
            ->selectRaw('Concat(Name, " - " , Country, " ", Zip, ". ", City, " ", Street, " ", HouseNumber) as Name, Id' )
            ->where('Customer', (int)($request->get('customer')))
            ->where('Deleted', 0)
            ->orderBy('Name')->get();
    }

}
