<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use mindtwo\LaravelMissingTranslations\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Run the translatable table migration
    foreach ([
        include dirname(__DIR__).'/../database/migrations/2024_01_05_163320_create_missing_translations_table.php',
        include dirname(__DIR__).'/migrations/create_users_table.php',
    ] as $migration) {
        $migration->up();
    }
});

test('registration of missing-translations route', function () {
    $this->get('/missing-translations')
        ->assertStatus(200);
});

test('denies access to unauthorized users if gate is enabled', function () {
    config([
        'missing-translations.authorization.gate' => true,
    ]);

    $this->get('/missing-translations')
        ->assertStatus(403);
});

test('gate can be defined to allow access', function () {
    config([
        'missing-translations.authorization.gate' => true,
    ]);

    Gate::define('viewMissingTranslations', function ($user) {
        return $user !== null;
    });

    $this->get('/missing-translations')
        ->assertStatus(403);

    // Create a user
    $user = User::create();

    $this->actingAs($user)->get('/missing-translations');
});

test('gate can have a custom name', function () {
    config([
        'missing-translations.authorization.gate' => 'customGate',
    ]);

    Gate::define('customGate', function ($user) {
        return $user !== null;
    });

    $this->get('/missing-translations')
        ->assertStatus(403);

    // Create a user
    $user = User::create();

    $this->actingAs($user)->get('/missing-translations');
});
