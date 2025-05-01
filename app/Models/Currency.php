<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Currency
 * @package App\Models
 * @version May 13, 2019, 1:50 pm UTC
 *
 * @property integer priority
 * @property string iso_code
 * @property string name
 * @property string symbol
 * @property string subunit
 * @property integer subunit_to_unit
 * @property string symbol_first
 * @property string html_entity
 * @property string decimal_mark
 * @property string thousands_separator
 * @property integer iso_numeric
 */
class Currency extends Model
{
    use SoftDeletes;

    public $table = 'currencies';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'priority',
        'iso_code',
        'name',
        'symbol',
        'subunit',
        'subunit_to_unit',
        'symbol_first',
        'html_entity',
        'decimal_mark',
        'thousands_separator',
        'iso_numeric'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'priority' => 'integer',
        'iso_code' => 'string',
        'name' => 'string',
        'symbol' => 'string',
        'subunit' => 'string',
        'subunit_to_unit' => 'integer',
        'symbol_first' => 'string',
        'html_entity' => 'string',
        'decimal_mark' => 'string',
        'thousands_separator' => 'string',
        'iso_numeric' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
