<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class QuantityUnitLang
 *
 * @package App\Models
 * @version January 19, 2022, 9:54 am UTC
 * @property integer $Lang
 * @property integer $QuantityUnit
 * @property string $Name
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuantityUnitLang whereRowModify($value)
 * @mixin Model
 */
class QuantityUnitLang extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'quantityunitlang';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Lang',
        'QuantityUnit',
        'Name',
        'RowCreate',
        'RowModify'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Lang' => 'integer',
        'QuantityUnit' => 'integer',
        'Name' => 'string',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Lang' => 'required|integer',
        'QuantityUnit' => 'required',
        'Name' => 'required|string|max:100',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
