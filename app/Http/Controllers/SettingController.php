<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;
use Response;
use myUser;
use SUAdatok;
use DB;
use DataTables;

class SettingController extends Controller
{
    /**
     * Display a listing of the Email Setting.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (myUser::check()) {

            return view('setting.edit');

        }
        return view('/');
    }

    /**
     * Display a listing of the Communication Setting.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function communicationIndex(Request $request)
    {
        if (myUser::check()) {

            return view('setting.communicationEdit');

        }
        return view('/');
    }

}
