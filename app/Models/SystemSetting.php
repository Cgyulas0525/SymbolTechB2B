<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SystemSetting
 *
 * @package App\Models
 * @version May 10, 2022, 9:51 am CEST
 * @property string $ProductKey
 * @property string $Company
 * @property string $Setting
 * @property string|\Carbon\Carbon $RowVersion
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|SystemSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereProductKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereSetting($value)
 * @method static \Illuminate\Database\Query\Builder|SystemSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SystemSetting withoutTrashed()
 * @mixin Model
 */
class SystemSetting extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'systemsetting';

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'ProductKey',
        'Company',
        'Setting',
        'RowVersion'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'ProductKey' => 'string',
        'Company' => 'string',
        'Setting' => 'string',
        'RowVersion' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'ProductKey' => 'nullable|string|max:65535',
        'Company' => 'nullable|string|max:65535',
        'Setting' => 'nullable|string|max:65535',
        'RowVersion' => 'nullable'
    ];


}
