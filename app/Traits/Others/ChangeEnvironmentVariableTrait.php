<?php

namespace App\Traits\Others;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

trait ChangeEnvironmentVariableTrait {

    public function changeEnvironmentVariable(Request $request) {

        $path = base_path('.env');
        $key = $request->get('key');
        $value = $request->get('value');

        $file = file_get_contents($path);
        if(is_bool(env($key)))
        {
            $old = env($key) ? 'true' : 'false';
        }
        elseif(env($key)===null){
            $old = 'null';
        }
        else{
            $old = env($key);
        }

        if (strlen($value) == 0) {
            $value = 'null';
        }

        $hol = (int)(strpos($file, $key."='".$old. "'"));
        if ( $hol > 0) {
            $mit  = $key."='".$old. "'";
            $mire = $key."='".$value. "'";
        } else {
            $mit  = $key."=".$old;
            $mire = $key."=".$value;
        }

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $mit, $mire, $file
            ));
        }

        Artisan::call('optimize:clear');

    }

}
