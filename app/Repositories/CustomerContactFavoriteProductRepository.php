<?php

namespace App\Repositories;

use App\Models\CustomerContactFavoriteProduct;
use App\Repositories\BaseRepository;

/**
 * Class CustomerContactFavoriteProductRepository
 * @package App\Repositories
 * @version May 24, 2022, 12:56 pm CEST
*/

class CustomerContactFavoriteProductRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customercontact_id',
        'product_id'
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
        return CustomerContactFavoriteProduct::class;
    }
}
