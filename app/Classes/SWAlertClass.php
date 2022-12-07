<?php
namespace App\Classes;

use URL;
use Alert;
use App\Classes\langClass;

Class SWAlertClass {

    public static function choice($id, $title, $cancelPath, $cancelText, $confirmPath, $confirmText)
    {
        Alert::question( langClass::trans($title))
            ->showCancelButton(
                $btnText = '<a class="swCancelButton" href="'. URL::asset($cancelPath) .'">' . langClass::trans($cancelText) .'</a>',
                $btnColor = '#ff0000')
            ->showConfirmButton(
                $btnText = '<a class="swConfirmButton" href="'. URL::asset($confirmPath) .'">' . langClass::trans($confirmText) .'</a>', // here is class for link
                $btnColor = '#0066cc',
            )->autoClose(false);
    }
}

