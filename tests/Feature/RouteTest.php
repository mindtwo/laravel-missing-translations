<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use mindtwo\LaravelMissingTranslations\Tests\RoutesTestCase;

uses(RoutesTestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Run the translatable table migration
    foreach ([
        include dirname(__DIR__).'/../database/migrations/2024_01_05_163320_create_missing_translations_table.php',
    ] as $migration) {
        $migration->up();
    }
});

test('registration of missing-translations route', function () {
    $this->updateConfig([
        'missing-translations.allowed_environments' => ['testing'],
    ]);

    $this->get('/missing-translations')
        ->assertStatus(200);
});

test('route is only available in allowed environments', function () {
    $this->updateConfig([
        'missing-translations.allowed_environments' => ['production'],
    ]);

    expect(config('missing-translations.allowed_environments'))->toBe(['production']);
    $this->get('/missing-translations')
        ->assertStatus(404);
});

test('registration of missing-translations route with custom prefix', function () {
    $this->updateConfig([
        'missing-translations.route.prefix' => 'custom-prefix',
        'missing-translations.allowed_environments' => ['testing'],
    ]);

    expect(config('missing-translations.route.prefix'))->toBe('custom-prefix');
    $this->get('/custom-prefix')
        ->assertStatus(200);

    $this->get('/missing-translations')
        ->assertStatus(404);
});
