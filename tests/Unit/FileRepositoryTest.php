<?php

// TODO

use Illuminate\Support\Facades\App;
use mindtwo\LaravelMissingTranslations\Repositories\File\FileRepository;
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

    App::useLangPath(str_replace('Unit', '', __DIR__).'lang');

    $this->translationKeys = [
        'example.test',
        'example.missing',
        'example.group.foo',
        'example.group.bar',
        'example.missing_group.foo',
        'hello',
        'world',
        'missing',
    ];

    $this->missingTranslationKeys = [
        'example.missing',
        'example.missing_group.foo',
        'missing',
    ];
});

test('retrieve a database repository instance', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('file');

    expect($repo instanceof FileRepository)->toBeTrue();

    expect($this->translationKeys)->toBeArray()
        ->and($this->translationKeys)->toBe([
            'example.test',
            'example.missing',
            'example.group.foo',
            'example.group.bar',
            'example.missing_group.foo',
            'hello',
            'world',
            'missing',
        ]);

    expect($this->missingTranslationKeys)->toBeArray()
        ->and($this->missingTranslationKeys)->toBe([
            'example.missing',
            'example.missing_group.foo',
            'missing',
        ]);
});

test('retrieve all missing translations model grouped by locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('file');

    $missingTranslations = $repo->getMissingTranslations(['en', 'de']);

    // The missing translations are grouped by locale and the translation key is the key
    expect($missingTranslations)->toBeArray()
        ->and($missingTranslations)->toBe([
            // en is main locale, so no missing keys
            'en' => [],
            'de' => [
                'example.missing' => 'Missing',
                'example.missing_group.foo' => 'Foo',
                'missing' => 'Missing',
            ],
        ]);
});

test('retrieve all missing translations model for a specific locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('file');

    $missingTranslations = $repo->getMissingTranslationsForLocale('de');

    expect($missingTranslations)->toBeArray()
        ->and($missingTranslations)->toBe([
            'example.missing' => 'Missing',
            'example.missing_group.foo' => 'Foo',
            'missing' => 'Missing',
        ]);
});

test('retrieve all missing translation keys grouped by locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('file');

    $missingTranslationKeys = $repo->getMissingTranslationKeys(['en', 'de']);

    expect($missingTranslationKeys)->toBeArray()
        ->and($missingTranslationKeys)->toBe([
            // en is main locale, so no missing keys
            'en' => [],
            'de' => [
                'example.missing',
                'example.missing_group.foo',
                'missing',
            ],
        ]);
});

test('retrieve all missing translation keys for a specific locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('file');

    $missingTranslationKeys = $repo->getMissingTranslationKeysForLocale('de');

    expect($missingTranslationKeys)->toBeArray();

    expect(array_diff($this->missingTranslationKeys, $missingTranslationKeys))->toBeEmpty();
});

test('retrieve all translation keys for a specific locale', function () {
    $missingTranslationsService = app()->make(MissingTranslations::class);

    $repo = $missingTranslationsService->repo('file');
    $translationKeys = $repo->getTranslationKeys('en');

    expect($translationKeys)->toBeArray();

    expect(array_diff($this->translationKeys, $translationKeys))->toBeEmpty();
});
