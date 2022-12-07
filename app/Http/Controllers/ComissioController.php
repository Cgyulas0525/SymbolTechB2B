<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;
use Response;
use myUser;
use SUAdatok;
use DB;
use DataTables;

class ComissioController extends Controller
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

                $data = SUAdatok::GetCountComissionList();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }

            return view('home');

        }
        return view('/');
    }

}
