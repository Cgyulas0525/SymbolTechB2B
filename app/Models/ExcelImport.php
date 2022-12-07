<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ExcelImport
 *
 * @package App\Models
 * @version April 27, 2022, 10:55 am CEST
 * @property string $Field0
 * @property string $Field1
 * @property string $Field2
 * @property string $Field3
 * @property string $Field4
 * @property string $Field5
 * @property string $Field6
 * @property string $Field7
 * @property string $Field8
 * @property string $Field9
 * @property string $Field10
 * @property string $Field11
 * @property string $Field12
 * @property string $Field13
 * @property string $Field14
 * @property string $Field15
 * @property string $Field16
 * @property string $Field17
 * @property string $Field18
 * @property string $Field19
 * @property integer $user_id
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExcelImport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField0($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField10($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField11($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField12($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField13($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField14($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField15($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField16($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField17($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField18($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField19($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField7($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField8($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereField9($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelImport whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ExcelImport withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExcelImport withoutTrashed()
 * @mixin Model
 */
class ExcelImport extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'excelimport';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'Field0',
        'Field1',
        'Field2',
        'Field3',
        'Field4',
        'Field5',
        'Field6',
        'Field7',
        'Field8',
        'Field9',
        'Field10',
        'Field11',
        'Field12',
        'Field13',
        'Field14',
        'Field15',
        'Field16',
        'Field17',
        'Field18',
        'Field19',
        'user_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'Field0' => 'string',
        'Field1' => 'string',
        'Field2' => 'string',
        'Field3' => 'string',
        'Field4' => 'string',
        'Field5' => 'string',
        'Field6' => 'string',
        'Field7' => 'string',
        'Field8' => 'string',
        'Field9' => 'string',
        'Field10' => 'string',
        'Field11' => 'string',
        'Field12' => 'string',
        'Field13' => 'string',
        'Field14' => 'string',
        'Field15' => 'string',
        'Field16' => 'string',
        'Field17' => 'string',
        'Field18' => 'string',
        'Field19' => 'string',
        'user_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Field0' => 'nullable|string|max:255',
        'Field1' => 'nullable|string|max:255',
        'Field2' => 'nullable|string|max:255',
        'Field3' => 'nullable|string|max:255',
        'Field4' => 'nullable|string|max:255',
        'Field5' => 'nullable|string|max:255',
        'Field6' => 'nullable|string|max:255',
        'Field7' => 'nullable|string|max:255',
        'Field8' => 'nullable|string|max:255',
        'Field9' => 'nullable|string|max:255',
        'Field10' => 'nullable|string|max:255',
        'Field11' => 'nullable|string|max:255',
        'Field12' => 'nullable|string|max:255',
        'Field13' => 'nullable|string|max:255',
        'Field14' => 'nullable|string|max:255',
        'Field15' => 'nullable|string|max:255',
        'Field16' => 'nullable|string|max:255',
        'Field17' => 'nullable|string|max:255',
        'Field18' => 'nullable|string|max:255',
        'Field19' => 'nullable|string|max:255',
        'user_id' => 'required|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    
}
