<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class XMLImportDetail
 *
 * @package App\Models
 * @version May 2, 2022, 11:30 am CEST
 * @property integer $xmlimport_id
 * @property string $tablename
 * @property integer $recordnumber
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail newQuery()
 * @method static \Illuminate\Database\Query\Builder|XMLImportDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail whereRecordnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail whereTablename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XMLImportDetail whereXmlimportId($value)
 * @method static \Illuminate\Database\Query\Builder|XMLImportDetail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|XMLImportDetail withoutTrashed()
 * @mixin Model
 */
class XMLImportDetail extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'xmlimportdetail';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'xmlimport_id',
        'tablename',
        'recordnumber'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'xmlimport_id' => 'integer',
        'tablename' => 'string',
        'recordnumber' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'xmlimport_id' => 'required|integer',
        'tablename' => 'required|string|max:100',
        'recordnumber' => 'required|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function xmlImport() {
        return $this->belongsTo(XMLImport::class, 'xmlimport_id', 'id');
    }

}
