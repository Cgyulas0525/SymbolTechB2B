<?php

namespace App\Repositories;

use App\Models\Translations;
use App\Repositories\BaseRepository;

/**
 * Class TranslationsRepository
 * @package App\Repositories
 * @version June 14, 2022, 8:50 am CEST
*/

class TranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'huname',
        'language',
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
        return Translations::class;
    }
}
