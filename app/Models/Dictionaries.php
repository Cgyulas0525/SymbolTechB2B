<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Dictionaries
 *
 * @package App\Models
 * @version February 16, 2022, 10:22 am CET
 * @property integer $tipus
 * @property string $nev
 * @property string $leiras
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries newQuery()
 * @method static \Illuminate\Database\Query\Builder|Dictionaries onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries whereLeiras($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries whereNev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries whereTipus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dictionaries whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Dictionaries withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Dictionaries withoutTrashed()
 * @mixin Model
 */
class Dictionaries extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'dictionaries';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'tipus',
        'nev',
        'leiras'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tipus' => 'integer',
        'nev' => 'string',
        'leiras' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'tipus' => 'required|integer',
        'nev' => 'required|string|max:191',
        'leiras' => 'nullable|string',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    
}
