<?php

namespace mindtwo\LaravelMissingTranslations\Models;

use Illuminate\Database\Eloquent\Model;

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
