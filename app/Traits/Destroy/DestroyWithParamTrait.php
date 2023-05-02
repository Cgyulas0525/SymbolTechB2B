<?php

namespace App\Traits\Destroy;

use App\Classes\Models\ModelPath;
use App\Classes\logClass;

trait DestroyWithParamTrait {

    public function destroyWithParam($table, $id, $route, $param) {

        $model_name = ModelPath::makeModelPath($table);
        $data = $model_name::find($id);

        if (empty($data)) {
            return redirect(route($route, $param));
        }

//        switch (strtolower($table)) {
//            case "contractannex":
//                $this->deletingContractAnnex($data);
//                break;
//            case "contractdeadline":
//                $this->deletingContractDeadLine($data);
//                break;
//            default:
//                echo "Your favorite color is neither red, blue, nor green!";
//        }

        $data->delete();

        logClass::insertDeleteRecord( 7, $table, $data->Id);

        return redirect(route($route,  $param));

    }

}
