<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use ddwClass;
use myUser;

/**
 * Class LogItemTable
 *
 * @package App\Models
 * @version February 14, 2022, 11:00 am UTC
 * @property integer $logitem_id
 * @property string $tablename
 * @property integer $recordid
 * @property integer $targetrecordid
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $customer_name
 * @property-read mixed $detail_count
 * @property-read mixed $event_date_time
 * @property-read mixed $event_name
 * @property-read mixed $user_name
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable newQuery()
 * @method static \Illuminate\Database\Query\Builder|LogItemTable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable query()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereLogitemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereRecordid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereTablename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereTargetrecordid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItemTable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LogItemTable withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LogItemTable withoutTrashed()
 * @mixin Model
 */
class LogItemTable extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'logitemtable';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'logitem_id',
        'tablename',
        'recordid',
        'targetrecordid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'logitem_id' => 'integer',
        'tablename' => 'string',
        'recordid' => 'integer',
        'targetrecordid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'logitem_id' => 'required|integer',
        'tablename' => 'required|string|max:100',
        'recordid' => 'nullable|integer',
        'targetrecordid' => 'nullable|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    protected $append = ['customerName', 'userName', 'eventName', 'eventDateTime', 'detailCount'];

    public function getCustomerNameAttribute()
    {
        $customer_id = LogItem::find($this->logitem_id)->customer_id;
        if ( $customer_id != -9999 ) {
            return Customer::find($customer_id)->Name;
        } else {
            return myUser::user()->name;
        }
    }

    public function getUserNameAttribute()
    {
        return Users::find(LogItem::find($this->logitem_id)->user_id)->name;
    }

    public function getEventNameAttribute()
    {
        return ddwClass::logEvent(LogItem::find($this->logitem_id)->eventtype);
    }

    public function getEventDateTimeAttribute()
    {
        return LogItem::find($this->logitem_id)->eventdatetime;
    }

    public function getDetailCountAttribute()
    {
        return LogItemTableDetail::where('logitemtable_id', $this->id)->get()->count();
    }

    public function logitem() {
        return $this->belongsTo(LogItem::class, 'logitem_id', 'id');
    }

    public function logitemtabledetail() {
        return $this->hasMany(LogItemTableDetail::class, 'logitemtable_id', 'id');
    }
}
