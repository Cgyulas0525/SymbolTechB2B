<?php
namespace App\Classes;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use DB;
use Flash;

Class imageUrl{

    public static function kepFeltolt($file) {

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $path = env('FRONT_END_URL').uniqid().'.'.$extension;
        $img = Image::make($file);
        $img->save(public_path($path));

 /*       $imageUrl = 'public/'.$path; */
        $imageUrl = $path;

        return $imageUrl;
    }

    public static function kepNev($file) {
        return "../" . $file;
    }

    public static function kepKicsi($file) {

        $extension = substr($file, strpos($file, '.') + 1, strlen($file));;

        $path = env('FRONT_END_URL').'kicsi/'.uniqid().'.'.$extension;
        $img = Image::make($file)->resize(40,40);
        $img->save(public_path($path));

/*        $imageUrl = 'public/'.$path;*/

        $imageUrl = $path;

        return $imageUrl;
    }

    public static function excelimport()
    {

        Flash::success('Customer Order saved successfully.')->important();

        return back();
    }


}
