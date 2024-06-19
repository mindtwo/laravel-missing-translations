<?php

namespace mindtwo\LaravelMissingTranslations\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MissingTranslation
 *
 * @property string $hash
 * @property string $string
 * @property string $locale
 */
class MissingTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hash',
        'string',
        'locale',
    ];
}
