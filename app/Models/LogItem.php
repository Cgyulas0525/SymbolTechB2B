<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use ddwClass;

/**
 * Class LogItem
 *
 * @package App\Models
 * @version February 28, 2022, 10:21 am CET
 * @property integer $customer_id
 * @property integer $user_id
 * @property integer $eventtype
 * @property string|\Carbon\Carbon $eventdatetime
 * @property string $remoteaddress
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $customer_name
 * @property-read mixed $event_name
 * @property-read mixed $user_name
 * @method static \Database\Factories\LogItemFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|LogItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereEventdatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereEventtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereRemoteaddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogItem whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|LogItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LogItem withoutTrashed()
 * @mixin Model
 */
class LogItem extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'logitem';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'customer_id',
        'user_id',
        'eventtype',
        'eventdatetime',
        'remoteaddress'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'user_id' => 'integer',
        'eventtype' => 'integer',
        'eventdatetime' => 'datetime',
        'remoteaddress' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'customer_id' => 'required|integer',
        'user_id' => 'required|integer',
        'eventtype' => 'required|integer',
        'eventdatetime' => 'required',
        'remoteaddress' => 'nullable|string|max:100',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    protected $append = ['customerName', 'userName', 'eventName', 'logItemTableRecord'];

    public function getCustomerNameAttribute()
    {
        return (!empty($this->customer_id) && ($this->customer_id != -9999)) ? Customer::find($this->customer_id)->Name : session('customer_name');
    }

    public function getUserNameAttribute()
    {
        return !empty($this->user_id) ? !empty(Users::find($this->user_id)->name) ? Users::find($this->user_id)->name : ' ' : ' ';
    }

    public function getEventNameAttribute()
    {
        return !empty($this->eventtype) ? ddwClass::logEvent($this->eventtype) : ' ';
    }

    public function getLogItemTableRecord()
    {
        return LogItemTable::where('logitem_id', $this->id)->count();
    }

}
