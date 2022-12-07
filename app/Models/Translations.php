<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Translations
 *
 * @package App\Models
 * @version June 14, 2022, 8:50 am CEST
 * @property string $huname
 * @property string $language
 * @property string $name
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\TranslationsFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Translations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Translations newQuery()
 * @method static \Illuminate\Database\Query\Builder|Translations onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Translations query()
 * @method static \Illuminate\Database\Eloquent\Builder|Translations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translations whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translations whereHuname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translations whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translations whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translations whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Translations withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Translations withoutTrashed()
 * @mixin Model
 */
class Translations extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'translations';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'huname',
        'language',
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'huname' => 'string',
        'language' => 'string',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'huname' => 'required|string|max:500',
        'language' => 'required|string|max:2',
        'name' => 'required|string|max:500',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    
}
