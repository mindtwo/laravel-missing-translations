<?php

return [
    'allowed_environments' => ['local', 'staging'],
    'main_locale' => 'en',
    'locales' => ['de'],
    'log_missing_keys' => true,

    'authorization' => [
        /*
         * The gate that checks if the current user can view the missing translations.
         */
        'gate' => false,

        /*
         * The middleware to use for the missing translations route.
         */
        'middleware' => ['web'],
    ],

    'route' => [
        /*
         * The prefix for the missing translations route.
         */
        'prefix' => 'missing-translations',
    ],

    'repositories' => [

        /*
         * The default repository used by the package to retrieve the missing translations.
         */
        'default' => 'file',

        /*
         * Source Classes used by the package to retrieve the missing translations.
         */
        'sources' => [
            'file' => \mindtwo\LaravelMissingTranslations\Repositories\File\FileRepository::class,

            'database' => \mindtwo\LaravelMissingTranslations\Repositories\Database\DatabaseRepository::class,
        ],

    ],
];
