<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;
use Response;
use myUser;
use App\Classes\adminClass;
use DB;
use DataTables;

class AdminController extends Controller
{
    /**
     * Display a listing of the B2B Customer Login.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function B2BCustomerLoginCountIndex(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = adminClass::B2BCustomerLoginCount(date('Y-m-d H:i:s', strtotime('today - 3 month')),
                                                          date('Y-m-d H:i:s', strtotime('today + 1 day')));

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }

            return view('home');

        }
        return view('/');
    }

}
