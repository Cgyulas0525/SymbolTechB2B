<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use myUser;
use App\Models\Customer;
use DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the Megrendelesfej.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = Customer::where('Deleted', 0)->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }

            return view('customer.index');;

        }
        return view('/');
    }

    public function dIndex(Request $request)
    {

        if (myUser::user()->rendszergazda === 0) {
            return view('dashboard.dashboard');
        }
        if (myUser::user()->rendszergazda === 1) {
            return view('dashboard.customerDashboard');
        }
        if (myUser::user()->rendszergazda === 2) {
            return view('dashboard.adminDashboard');
        }

    }

}
