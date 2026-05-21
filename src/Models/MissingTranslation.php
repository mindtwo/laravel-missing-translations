<?php

namespace mindtwo\LaravelMissingTranslations\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $hash
 * @property string $string
 * @property string $locale
 */
class MissingTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hash',
        'string',
        'locale',
    ];
}
