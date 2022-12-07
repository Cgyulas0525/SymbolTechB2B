<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Apimodel
 * @package App\Models
 * @version September 21, 2022, 9:54 am CEST
 *
 * @property integer $api_id
 * @property string $model
 * @property integer $recordnumber
 * @property integer $insertednumber
 * @property integer $updatednumber
 * @property integer $errornumber
 */
class Apimodel extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'apimodel';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'api_id',
        'model',
        'recordnumber',
        'insertednumber',
        'updatednumber',
        'errornumber'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'api_id' => 'integer',
        'model' => 'string',
        'recordnumber' => 'integer',
        'insertednumber' => 'integer',
        'updatednumber' => 'integer',
        'errornumber' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'api_id' => 'required|integer',
        'model' => 'required|string|max:100',
        'recordnumber' => 'required|integer',
        'insertednumber' => 'required|integer',
        'updatednumber' => 'required|integer',
        'errornumber' => 'required|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function apimodelerror() {
        return $this->hasMany('App\Models\Apimodelerror');
    }

    public function api() {
        return $this->belongsTo('App\Models\Api');
    }


}
