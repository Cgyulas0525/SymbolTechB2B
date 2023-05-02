<?php

namespace App\Traits\Destroy;

use App\Classes\Models\ModelPath;
use App\Classes\SWAlertClass;

trait BeforeDestroysWithParamTrait {

    public function beforeDestroysWithParam($table, $id, $route, $param = NULL) {

        $view = 'layouts.show';
        $model_name = ModelPath::makeModelPath($table);
        $data = $model_name::find($id);
        $text = 'Törlődik a tétel és a hozzá kapcsolódó adatok! Biztos, hogy törli a tételt?';

        SWAlertClass::choice($id, $text, '/'.$route. '/' . $param, 'Kilép', '/destroyWithParam/'.$table.'/'.$id.'/'.$route. '/'.$param, 'Töröl');

        return view($view)->with('table', $data);

    }

}
