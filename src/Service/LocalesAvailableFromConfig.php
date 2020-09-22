<?php

declare(strict_types=1);

namespace mindtwo\Service\LaravelMissingTranslations;

use Illuminate\Support\Arr;

class LocalesAvailableFromConfig implements LocalesAvailableInterface
{
    function getMainLocale(): string
    {
        return (string) config('missing-translations.main_locale');
    }

    function getAllLocales(): array
    {
        return Arr::wrap(config('missing-translations.locales'));
    }
}
