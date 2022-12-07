<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Apimodelerror
 * @package App\Models
 * @version September 26, 2022, 10:07 am CEST
 *
 * @property integer $apimodel_id
 * @property string $smtp
 * @property string $error
 */
class Apimodelerror extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'apimodelerror';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'apimodel_id',
        'smtp',
        'error'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'apimodel_id' => 'integer',
        'smtp' => 'string',
        'error' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'apimodel_id' => 'required|integer',
        'smtp' => 'required|string|max:2000',
        'error' => 'required|string|max:2000',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function apimodel() {
        return $this->belongsTo('App\Models\Apimodel');
    }
}
