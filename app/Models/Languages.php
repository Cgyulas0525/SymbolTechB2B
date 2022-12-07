<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Languages
 *
 * @package App\Models
 * @version June 14, 2022, 8:50 am CEST
 * @property string $shortname
 * @property string $name
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $detail_number
 * @property-read mixed $translated_number
 * @property-read mixed $untranslated_number
 * @method static \Database\Factories\LanguagesFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Languages newQuery()
 * @method static \Illuminate\Database\Query\Builder|Languages onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Languages query()
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Languages withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Languages withoutTrashed()
 * @mixin Model
 */
class Languages extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'languages';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'shortname',
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'shortname' => 'string',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'shortname' => 'required|string|max:2',
        'name' => 'required|string|max:100',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    protected $append = [
        'DetailNumber',
        'TranslatedNumber',
        'UntranslatedNumber',
    ];

    public function getDetailNumberAttribute() {
        return Translations::where('language', $this->shortname)->get()->count();
    }

    public function getTranslatedNumberAttribute() {
        return Translations::where('language', $this->shortname)
                        ->whereColumn('huname', '!=', 'name')
                        ->get()
                        ->count();
    }

    public function getUntranslatedNumberAttribute() {
        return Translations::where('language', $this->shortname)
                        ->whereColumn('huname', '=', 'name')
                        ->get()
                        ->count();
    }

}
