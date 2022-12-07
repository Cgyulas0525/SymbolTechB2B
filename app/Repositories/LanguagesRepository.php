<?php

namespace App\Repositories;

use App\Models\Languages;
use App\Repositories\BaseRepository;

/**
 * Class LanguagesRepository
 * @package App\Repositories
 * @version June 14, 2022, 8:50 am CEST
*/

class LanguagesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'shortname',
        'name'
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
        return Languages::class;
    }
}
