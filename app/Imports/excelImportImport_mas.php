<?php
namespace App\Imports;

use App\Models\ExcelImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class excelImportImport implements ToModel, WithCalculatedFormulas
{

    use Importable;

    public function model(array $row)
    {

        $name     = intval( !empty(session('excelCode')) ? session('excelCode') : 0) == 0 ? 0 : intval(session('excelCode'));
        $quantity = intval( !empty(session('excelQuantity')) ? session('excelQuantity') : 0) == 0 ? 0 : intval(session('excelQuantity'));
        return new ExcelImport([
            'Name'     => $row[$name == 0 ? $name : $name - 1],
            'Quantity' => $row[$quantity == 0 ? 1 : $quantity - 1]
        ]);
    }
}

