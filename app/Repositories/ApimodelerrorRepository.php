<?php

namespace App\Repositories;

use App\Models\Apimodelerror;
use App\Repositories\BaseRepository;

/**
 * Class ApimodelerrorRepository
 * @package App\Repositories
 * @version September 26, 2022, 10:07 am CEST
*/

class ApimodelerrorRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'apimodel_id',
        'smtp',
        'error'
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
        return Apimodelerror::class;
    }
}
