<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductCategoryExport implements FromCollection //, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings() :array
    {
        return [
            'Id',
            'Name',
            'Parent',
            'LeftValue',
            'RightValue',
            'ProfitPercent',
            'PriceDigits',
            'PriceDigitsExt',
            'Vat',
            'VatBuy',
            'Service',
            'QuantityUnit',
            'QuantityDigits',
            'CustomsTariffNumber',
            'GuaranteeMonths',
            'GuaranteeMode',
            'GuaranteeMinUnitPrice',
            'GuaranteeDescription',
            'BarcodeMask',
            'MinProfitPercent',
            'PriceCategoryRule',
            'VoucherRules',
            'UseWarrantyRule',
            'EuVat',
            'EuVatBuy',
            'NonEuVat',
            'NonEuVatBuy',
        ];
    }
}
