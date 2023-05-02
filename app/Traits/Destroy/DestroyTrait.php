<?php

namespace App\Traits\Destroy;

use App\Classes\logClass;
use App\Classes\Models\ModelPath;

trait DestroyTrait {

    public function destroy($table, $id, $route) {

        $route .= '.index';
        $model_name = ModelPath::makeModelPath($table);
        $data = $model_name::find($id);

        if (empty($data)) {
            return redirect(route($route));
        }

        $data->delete();

        logClass::insertDeleteRecord( 7, $table, $data->Id);

        return redirect(route($route));

    }

}
