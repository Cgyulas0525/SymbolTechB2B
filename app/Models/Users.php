<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Users
 *
 * @package App\Models
 * @version June 23, 2022, 2:26 pm CEST
 * @property string $name
 * @property string $email
 * @property string|\Carbon\Carbon $email_verified_at
 * @property string $password
 * @property integer $employee_id
 * @property integer $customercontact_id
 * @property integer $rendszergazda
 * @property string $megjegyzes
 * @property integer $CustomerAddress
 * @property integer $TransportMode
 * @property string $remember_token
 * @property string $image_url
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\UsersFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Users newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Users newQuery()
 * @method static \Illuminate\Database\Query\Builder|Users onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Users query()
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereCustomercontactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereMegjegyzes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereRendszergazda($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereTransportMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Users withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Users withoutTrashed()
 * @mixin Model
 */
class Users extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'users';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'employee_id',
        'customercontact_id',
        'rendszergazda',
        'megjegyzes',
        'CustomerAddress',
        'TransportMode',
        'remember_token',
        'image_url'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'email_verified_at' => 'datetime',
        'password' => 'string',
        'employee_id' => 'integer',
        'customercontact_id' => 'integer',
        'rendszergazda' => 'integer',
        'megjegyzes' => 'string',
        'CustomerAddress' => 'integer',
        'TransportMode' => 'integer',
        'remember_token' => 'string',
        'image_url' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'nullable|string|max:191',
        'email' => 'nullable|string|max:191',
        'email_verified_at' => 'nullable',
        'password' => 'nullable|string|max:191',
        'employee_id' => 'nullable|integer',
        'customercontact_id' => 'nullable|integer',
        'rendszergazda' => 'nullable|integer',
        'megjegyzes' => 'nullable|string',
        'CustomerAddress' => 'nullable',
        'TransportMode' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable',
        'remember_token' => 'nullable|string|max:100',
        'image_url' => 'nullable|string|max:191'
    ];

    protected $append = [
        'customerName',
        'customerId',
        'rgnev',
        'B2BLoginCount'
    ];

    public function getCustomerNameAttribute() {
        if (empty($this->customercontact_id)) {
            return '';
        } else {
            $name = Customer::where('Id', function ($query) {
                $query->from('customercontact')->select('Customer')->where('Id', function ($query) {
                    $query->from('users')->select('customercontact_id')->where('id', $this->id)->first();
                })->first();
            })->first()->Name;
            return !empty($name) ? $name : '';
        }
    }

    public function getCustomerIdAttribute() {
        if (empty($this->customercontact_id)) {
            return null;
        } else {
            return Customer::where('Id', function ($query) {
                $query->from('customercontact')->select('Customer')->where('Id', function ($query) {
                    $query->from('users')->select('customercontact_id')->where('id', $this->id)->first();
                })->first();
            })->first()->Id;
        }
    }

    public function getRgNevAttribute() {
        return !empty($this->rendszergazda) ? Dictionaries::find($this->rendszergazda)->nev : null;
    }

    public function getB2BLoginCountAttribute() {
        return LogItem::where('user_id', $this->id)->get()->count();
    }
}
