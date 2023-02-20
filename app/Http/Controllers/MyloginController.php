<?php

namespace App\Http\Controllers;

use App\Classes\langClass;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Flash;
use Response;
use DB;
use logClass;
use utilityClass;
use App\Models\Users;

class MyloginController extends Controller
{
    public static function login(Request $request)
    {

        $name = $request->name;
        if ($name === "administrator") {
            if ((env('INSTALL_STATUS') == 2) && (env('MAIL_SET') == 1)) {
                return redirect(route('myLogin'));
            }
        }

        $password = $request->password;

        if (empty($name)) {
            Flash::error(langClass::trans('A név kötelező!'))->important();
            return back();
        }

        if (empty($password)) {
            return back();
        }

        $user = Users::where('name', $name)
            ->where('password', md5($password))
            ->first();


        if (empty($user)) {
            Flash::error(langClass::trans('Hibás név vagy jelszó!'))->important();
            return back();
        }

        $customer = new Customer;

        if (!empty($user->employee_id)) {
            $customer->Id = -9999;
            $customer->Name = 'Symbol Tech Zrt';
            $employee = Employee::where('Id', $user->employee_id)->first();
        } else {
            if (!empty($user->customercontact_id)) {
                $id = $user->customercontact_id;
                $customer = Customer::where('Id', function ($query) use($id) {
                    return $query->select('Customer')->from('customercontact')->where('Id', $id)->first();
                })->first();
            }
        }

        session(['user_id' => $user->id]);
        session(['user_rendszergazda' => $user->rendszergazda]);
        session(['noAviablePicture' => utilityClass::noAviablePicture()]);
        if ($user->id == 0) {
                session(['user_picture' => NULL ]);

                /* ide kell a systemsetting tábla customer oszlopából a partner */

                session(['customer_id' => -9999]);
                session(['customer_name' => '']);
        } else {
            if (!empty($user->employee_id)) {
                session(['user_picture' => !empty($employee) ? $employee->Picture : null]);
            } else {
                session(['user_picture' => NULL ]);
            }
            if (!empty($customer->Id)) {
                session(['customer_id' => $customer->Id]);
                session(['customer_name' => $customer->Name]);
            }
        }

        if (Employee::count() != 0) {
            logClass::insertLogIn(1);
        }

        return view('home');
    }

    public function settingIndex(Request $request)
    {
        return view('setting.edit');
    }


}
