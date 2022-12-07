<?php

namespace App\Repositories;

use App\Models\ShoppingCartDetail;
use App\Repositories\BaseRepository;

/**
 * Class ShoppingCartDetailRepository
 * @package App\Repositories
 * @version March 9, 2022, 2:14 pm CET
*/

class ShoppingCartDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ShoppingCart',
        'Currency',
        'CurrencyRate',
        'Product',
        'Vat',
        'QuantityUnit',
        'Reverse',
        'Quantity',
        'CustomerOfferDetail',
        'CustomerContractDetail',
        'UnitPrice',
        'DiscountPercent',
        'DiscountUnitPrice',
        'GrossPrices',
        'DepositValue',
        'DepositPercent',
        'NetValue',
        'GrossValue',
        'VatValue',
        'Comment',
        'CustomerOrderDetail'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ShoppingCartDetail::class;
    }
}
