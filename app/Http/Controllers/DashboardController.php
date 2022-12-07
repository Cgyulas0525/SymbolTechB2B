<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;
use Response;
use myUser;
use SUAdatok;
use DB;
use DataTables;

use dashboardClass;

class DashboardController extends Controller
{
    /**
     * Display a listing of the Megrendelesfej.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function CustomerOrderInterval(Request $request, $from, $to)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = dashboardClass::CustomerOrderInterval($from, $to);

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }

            return view('home');

        }
        return view('/');
    }

}
