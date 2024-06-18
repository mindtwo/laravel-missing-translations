<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use mindtwo\LaravelMissingTranslations\Controllers;

// Development
Route::get('/'. config('missing-translations.route.prefix', 'missing-translations'), [Controllers\MissingTranslationsController::class, 'show'])
    ->middleware(config('missing-translations.authorization.middleware'))
    ->name('missing-translations.show');
