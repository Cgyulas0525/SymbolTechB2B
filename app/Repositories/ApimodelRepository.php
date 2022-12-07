<?php

namespace App\Repositories;

use App\Models\Apimodel;
use App\Repositories\BaseRepository;

/**
 * Class ApimodelRepository
 * @package App\Repositories
 * @version September 21, 2022, 9:54 am CEST
*/

class ApimodelRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'api_id',
        'model',
        'recordnumber',
        'insertednumber',
        'updatednumber',
        'errornumber'
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
        return Apimodel::class;
    }
}
