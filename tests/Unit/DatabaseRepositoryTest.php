<?php

use mindtwo\LaravelMissingTranslations\Models\MissingTranslation;
use mindtwo\LaravelMissingTranslations\Repositories\Database\DatabaseRepository;
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;
use mindtwo\LaravelMissingTranslations\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // Run the translatable table migration
    foreach ([
        include dirname(__DIR__).'/../database/migrations/2024_01_05_163320_create_missing_translations_table.php',
    ] as $migration) {
        $migration->up();
    }

    $mocks = [
        'en' => [
            'missing_translation_key_1',
            'missing_translation_key_2',
        ],
        'de' => [
            'missing_translation_key_de_1',
            'missing_translation_key_de_2',
        ],
    ];

    foreach ($mocks as $locale => $keys) {
        foreach ($keys as $key) {
            MissingTranslation::create([
                'hash' => md5($key),
                'locale' => $locale,
                'string' => $key,
            ]);
        }
    }
});

test('retrieve a database repository instance', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('database');

    expect($repo instanceof DatabaseRepository)->toBeTrue();
});

test('retrieve all missing translations model grouped by locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('database');

    $missingTranslations = $repo->getMissingTranslations(['en', 'de']);

    // The missing translations are grouped by locale and the translation key is the key
    expect($missingTranslations)->toBeArray()
        ->and($missingTranslations)->toBe([
            'en' => [
                'missing_translation_key_1' => '',
                'missing_translation_key_2' => '',
            ],
            'de' => [
                'missing_translation_key_de_1' => '',
                'missing_translation_key_de_2' => '',
            ],
        ]);
});

test('retrieve all missing translations model for a specific locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('database');

    $missingTranslations = $repo->getMissingTranslationsForLocale('en');

    expect($missingTranslations)->toBeArray()
        ->and($missingTranslations)->toBe([
            'missing_translation_key_1' => '',
            'missing_translation_key_2' => '',
        ]);
});

test('retrieve all missing translation keys grouped by locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('database');

    $missingTranslationKeys = $repo->getMissingTranslationKeys(['en', 'de']);

    expect($missingTranslationKeys)->toBeArray()
        ->and($missingTranslationKeys)->toBe([
            'en' => [
                'missing_translation_key_1',
                'missing_translation_key_2',
            ],
            'de' => [
                'missing_translation_key_de_1',
                'missing_translation_key_de_2',
            ],
        ]);
});

test('retrieve all missing translation keys for a specific locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('database');

    $missingTranslationKeys = $repo->getMissingTranslationKeysForLocale('en');

    expect($missingTranslationKeys)->toBeArray()
        ->and($missingTranslationKeys)->toBe([
            'missing_translation_key_1',
            'missing_translation_key_2',
        ]);
});

test('retrieve all translation keys for a specific locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('database');
    $translationKeys = $repo->getTranslationKeys('en');
})->skip(true, 'We use the file repository for this.');
