<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use mindtwo\LaravelMissingTranslations\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // Run the translatable table migration
    foreach ([
        include dirname(__DIR__).'/../database/migrations/2024_01_05_163320_create_missing_translations_table.php',
        include dirname(__DIR__).'/database/migrations/create_users_table.php',
    ] as $migration) {
        $migration->up();
    }

    App::useLangPath(str_replace('Feature', '', __DIR__).'lang');

    // Reload translations
    Lang::setLoaded([]);
    Lang::load('*', '*', 'en');
    Lang::load('*', '*', 'de');

    config([
        'missing-translations.log_paused' => false,
    ]);
});

test('create an entry if missing key is not set', function () {
    App::setLocale('de');
    expect(App::getLocale())->toBe('de');

    $missing = __('missing');

    expect($missing)->toBe('missing');

    // Check if the missing translation was logged
    $this->assertDatabaseHas('missing_translations', [
        'hash' => md5('missing'),
        'string' => 'missing',
        'locale' => 'de',
    ]);
});

test('do not create an entry if logging is paused', function () {
    App::setLocale('de');
    expect(Lang::getLocale())->toBe('de');

    config([
        'missing-translations.log_paused' => true,
    ]);

    $missing = __('missing');

    expect($missing)->toBe('missing');

    // Check if the missing translation was logged
    $this->assertDatabaseMissing('missing_translations', [
        'hash' => md5('missing'),
        'string' => 'missing',
        'locale' => 'de',
    ]);
});
