<?php

namespace App\Traits\Others;

use App\Models\CustomerContact;
use Illuminate\Http\Request;

trait CustomerContactDDWTrait {

    /*
 * Customer kontaktjai DDW
 *
 * @param $request
 *
 * @return array
 */
    public static function customerContactDDW(Request $request) {
        return CustomerContact::where('Deleted', 0)
            ->where('Customer', $request->get('customer'))
            ->whereNotIn('Id', function ($query) {
                return $query->from('users')->select('customercontact_id')->whereNotNull('customercontact_id')->whereNull('deleted_at')->get();
            })
            ->select('Name', 'Id')
            ->orderBy('Name')->get();
    }

}
