<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Country
 * @package App\Models
 * @version May 13, 2019, 2:04 pm UTC
 *
 * @property string code
 * @property string name
 */
class Country extends Model
{
    use SoftDeletes;
    // You could add HasFactory here if you plan to use factories
    // use SoftDeletes, HasFactory;

    // These table and timestamp constants are not required in Laravel 12
    // as they match the Laravel defaults, but they don't cause any problems
    public $table = 'countries';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // In Laravel 12, this is handled by the SoftDeletes trait automatically
    // But keeping it doesn't cause issues
    protected $dates = ['deleted_at'];

    // This is still valid in Laravel 12
    public $fillable = [
        'code',
        'name'
    ];

    /**
     * The attributes that should be cast.
     *
     * The naming in Laravel 12 is typically "cast" rather than "casted"
     * but both work - this will function correctly
     */
    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * This is not a Laravel core feature but likely part of a package
     * you're using. It's fine to keep if you're using that package.
     */
    public static $rules = [
        // Empty rules array
    ];
}
