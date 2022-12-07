<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class LogItemTableDetail
 *
 * @package App\Models
 * @version February 14, 2022, 11:00 am UTC
 * @property integer $logitemtable_id
 * @property string $changedfield
 * @property integer $oldinteger
 * @property integer $oldstring
 * @property integer $olddatetime
 * @property number $olddecimal
 * @property integer $newinteger
 * @property integer $newstring
 * @property integer $newdatetime
 * @property number $newdecimal
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail newQuery()
 * @method static \Illuminate\Database\Query\Builder|LogItemTableDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereChangedfield($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereLogitemtableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereNewdatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereNewdecimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereNewinteger($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereNewstring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereOlddatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereOlddecimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereOldinteger($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereOldstring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTableDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LogItemTableDetail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LogItemTableDetail withoutTrashed()
 * @mixin Model
 */
class LogItemTableDetail extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'logitemtabledetail';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'logitemtable_id',
        'changedfield',
        'oldinteger',
        'oldstring',
        'olddatetime',
        'olddecimal',
        'newinteger',
        'newstring',
        'newdatetime',
        'newdecimal'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'logitemtable_id' => 'integer',
        'changedfield' => 'string',
        'oldinteger' => 'integer',
        'oldstring' => 'string',
        'olddatetime' => 'datetime',
        'olddecimal' => 'decimal:4',
        'newinteger' => 'integer',
        'newstring' => 'string',
        'newdatetime' => 'datetime',
        'newdecimal' => 'decimal:4'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'logitemtable_id' => 'required|integer',
        'changedfield' => 'required|string|max:100'
    ];

    public function oldValue() {
        if ($this->oldinteger !== null) {
            return $this->oldinteger;
        } else {
            if ($this->olddecimal !== null) {
                return number_format($this->olddecimal,4,",",".");
            } else {
                if ($this->oldstring !== null) {
                    return $this->oldstring;
                } else {
                    if ($this->olddatetime !== null) {
                        return date('Y.m.d h.m.s', strtotime($this->olddatetime));
                    } else {
                        return '';
                    }
                }
            }
        }
    }

    public function newValue() {
        if ($this->newinteger !== null) {
            return $this->newinteger;
        } else {
            if ($this->newdecimal !== null) {
                return number_format($this->newdecimal,4,",",".");
            } else {
                if ($this->newstring !== null) {
                    return $this->newstring;
                } else {
                    if ($this->newdatetime !== null) {
                        return date('Y.m.d h.m.s', strtotime($this->newdatetime));
                    } else {
                        return '';
                    }
                }
            }
        }
    }

}
