<?php

namespace App\Repositories;

use App\Models\LogItem;
use App\Repositories\BaseRepository;

/**
 * Class LogItemRepository
 * @package App\Repositories
 * @version February 28, 2022, 10:21 am CET
*/

class LogItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_id',
        'user_id',
        'eventtype',
        'eventdatetime',
        'remoteaddress'
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
        return LogItem::class;
    }
}
