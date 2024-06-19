<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\Database;

use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;
use mindtwo\LaravelMissingTranslations\Models\MissingTranslation;
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;

class DatabaseRepository implements MissingTranslationRepository
{
    public function __construct() {}

    /**
     * Get missing translations for the specified locales.
     *
     * @return array - array of missing translations with key grouped by locale
     */
    public function getMissingTranslations(array $locales): array
    {
        // Get all missing translations for the specified locales via the MissingTranslation model
        return MissingTranslation::whereIn('locale', $locales)
            ->get()
            ->groupBy('locale')
            ->mapWithKeys(function ($items, $locale) {
                return [
                    $locale => $items->reduce(function ($carry, $item) {
                        $carry[$item['string']] = '';

                        return $carry;
                    }, []),
                ];
            })
            ->toArray();
    }

    /**
     * Get missing translations for the specified locale.
     *
     * @return array - array of missing translations with key
     */
    public function getMissingTranslationsForLocale(string $locale): array
    {
        // Get all missing keys for the specified locale via the MissingTranslation model
        $missingKeys = $this->getMissingTranslationKeysForLocale($locale);

        return collect($missingKeys)
            ->mapWithKeys(function ($key) {
                return [$key => ''];
            })
            ->toArray();
    }

    /**
     * Get missing translation keys for the specified locales.
     *
     * @return array - array of missing translations keys grouped by locale
     */
    public function getMissingTranslationKeys(array $locales): array
    {
        return MissingTranslation::whereIn('locale', $locales)
            ->get()
            ->reduce(function ($carry, $item) {
                $carry[$item['locale']][] = $item['string'];

                return $carry;
            }, []);
    }

    /**
     * Get missing translation keys for the specified locale.
     *
     * @return array - array of missing translations keys
     */
    public function getMissingTranslationKeysForLocale(string $locale): array
    {
        return MissingTranslation::where('locale', $locale)
            ->pluck('string')
            ->toArray();
    }

    /**
     * Get the translation keys for the specified locale.
     *
     * @return array - array of translation keys
     */
    public function getTranslationKeys(string $locale): array
    {
        // Does not support default translation keys
        return app(MissingTranslations::class)->repo('file')->getTranslationKeys($locale);
    }
}
