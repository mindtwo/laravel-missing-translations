<?php

use mindtwo\LaravelMissingTranslations\Controllers;

// Development
if (App::environment(config('missing-translations.allowed_environments'))) {
    Route::get('/missing-translations', [Controllers\MissingTranslationsController::class, 'show'])->name('missing-translations.show');
}