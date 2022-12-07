<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Write Your Code.
     *
     * @return string
     */
    public function index()
    {
        return view('lang');
    }

    /**
     * Write Your Code..
     *
     * @return string
     */
    public function langChange(Request $request)
    {
        App::setLocale($request->lang);
        session()->put('locale',$request->lang);

        if (strpos(back()->getTargetUrl(), 'login')) {
            return redirect('/dIndex');
        } else {
            return redirect()->back();
        }
    }
}
