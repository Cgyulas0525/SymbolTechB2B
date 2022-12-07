<?php
namespace App\Imports;

use App\Models\ExcelImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithProgressBar;

use myUser;

class excelImportImport implements ToModel, WithCalculatedFormulas
{

    use Importable;

    public function model(array $row)
    {
        return new ExcelImport([
            'Field0' => isset($row[0]) ? $row[0] : null,
            'Field1' => isset($row[1]) ? $row[1] : null,
            'Field2' => isset($row[2]) ? $row[2] : null,
            'Field3' => isset($row[3]) ? $row[3] : null,
            'Field4' => isset($row[4]) ? $row[4] : null,
            'Field5' => isset($row[5]) ? $row[5] : null,
            'Field6' => isset($row[6]) ? $row[6] : null,
            'Field7' => isset($row[7]) ? $row[7] : null,
            'Field8' => isset($row[8]) ? $row[8] : null,
            'Field9' => isset($row[9]) ? $row[9] : null,
            'Field10' => isset($row[10]) ? $row[10] : null,
            'Field11' => isset($row[11]) ? $row[11] : null,
            'Field12' => isset($row[12]) ? $row[12] : null,
            'Field13' => isset($row[13]) ? $row[13] : null,
            'Field14' => isset($row[14]) ? $row[14] : null,
            'Field15' => isset($row[15]) ? $row[15] : null,
            'Field16' => isset($row[16]) ? $row[16] : null,
            'Field17' => isset($row[17]) ? $row[17] : null,
            'Field18' => isset($row[18]) ? $row[18] : null,
            'Field19' => isset($row[19]) ? $row[19] : null,
            'user_id' => myUser::user()->id
        ]);
    }
}

