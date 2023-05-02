<?php

namespace App\Traits\Destroy;

use App\Classes\Models\ModelPath;
use App\Classes\SWAlertClass;

trait BeforeDestroyTrait {

    public function beforeDestroys($table, $id, $route) {

        $view = 'layouts.show';
        $model_name = ModelPath::makeModelPath($table);
        $data = $model_name::find($id);

        SWAlertClass::choice($id, 'Biztos, hogy törli a tételt?', '/'.$route, 'Kilép', '/destroy/'.$table.'/'.$id.'/'.$route, 'Töröl');

        return view($view)->with('table', $data);

    }

}
