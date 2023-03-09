<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SystemSettingValue
 *
 * @package App\Models
 * @version May 10, 2022, 9:52 am CEST
 * @property string $Name
 * @property integer $ValueType
 * @property integer $ValueBool
 * @property integer $ValueInt
 * @property number $ValueDecimal
 * @property string|\Carbon\Carbon $ValueDate
 * @property integer $ValueBigInt
 * @property string $ValueString
 * @property string $ValueText
 * @property string $ValueBinary
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue newQuery()
 * @method static \Illuminate\Database\Query\Builder|SystemSettingValue onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueBigInt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueBinary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueBool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueDecimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueInt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueString($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSettingValue whereValueType($value)
 * @method static \Illuminate\Database\Query\Builder|SystemSettingValue withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SystemSettingValue withoutTrashed()
 * @mixin Model
 */
class SystemSettingValue extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'systemsettingvalue';
    public $timestamps = false;

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'Name',
        'ValueType',
        'ValueBool',
        'ValueInt',
        'ValueDecimal',
        'ValueDate',
        'ValueBigInt',
        'ValueString',
        'ValueText',
        'ValueBinary',
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
        'Name' => 'string',
        'ValueType' => 'integer',
        'ValueBool' => 'integer',
        'ValueInt' => 'integer',
        'ValueDecimal' => 'decimal:4',
        'ValueDate' => 'datetime',
        'ValueBigInt' => 'integer',
        'ValueString' => 'string',
        'ValueText' => 'string',
        'ValueBinary' => 'string',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Name' => 'required|string|max:100',
        'ValueType' => 'required|integer',
        'ValueBool' => 'nullable',
        'ValueInt' => 'nullable|integer',
        'ValueDecimal' => 'nullable|numeric',
        'ValueDate' => 'nullable',
        'ValueBigInt' => 'nullable',
        'ValueString' => 'nullable|string|max:100',
        'ValueText' => 'nullable|string|max:65535',
        'ValueBinary' => 'nullable|string|max:65535',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
