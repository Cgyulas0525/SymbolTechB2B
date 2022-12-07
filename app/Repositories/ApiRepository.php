<?php

namespace App\Repositories;

use App\Models\Api;
use App\Repositories\BaseRepository;

/**
 * Class ApiRepository
 * @package App\Repositories
 * @version September 21, 2022, 9:54 am CEST
*/

class ApiRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'filename'
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
        return Api::class;
    }
}
