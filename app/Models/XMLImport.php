<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class XMLImport
 *
 * @package App\Models
 * @version May 2, 2022, 11:30 am CEST
 * @property integer $user_id
 * @property integer $ok
 * @property string $error
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport newQuery()
 * @method static \Illuminate\Database\Query\Builder|XMLImport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport query()
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport whereOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImport whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|XMLImport withTrashed()
 * @method static \Illuminate\Database\Query\Builder|XMLImport withoutTrashed()
 * @mixin Model
 */
class XMLImport extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'xmlimport';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'user_id',
        'ok',
        'error'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'ok' => 'integer',
        'error' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|integer',
        'ok' => 'required|integer',
        'error' => 'nullable|string|max:500',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    
}
