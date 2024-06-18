<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\File;

use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;
use mindtwo\LaravelMissingTranslations\Repositories\File\Actions\CollectMissingTranslationsAction;
use mindtwo\LaravelMissingTranslations\Repositories\File\Actions\CollectTranslationsAction;

class FileRepository implements MissingTranslationRepository
{

    public function __construct(
        protected CollectMissingTranslationsAction $collectMissingTranslationsAction,
        protected CollectTranslationsAction $collectTranslationsAction,
    )
    {

    }

    /**
     * Get missing translations for the specified locales.
     *
     * @param array $locales
     * @return array - array of missing translations with key grouped by locale
     */
    public function getMissingTranslations(array $locales): array
    {
        return collect($locales)
            ->mapWithKeys(function ($locale) {
                $missing = collect(($this->collectMissingTranslationsAction)($locale));

                return [$locale => $missing->toArray()];
            })
            ->toArray();
    }

    /**
     * Get missing translations for the specified locale.
     *
     * @param string $locale
     * @return array
     */
    public function getMissingTranslationsForLocale(string $locale): array
    {
        return collect(($this->collectMissingTranslationsAction)($locale))->keys()->toArray();
    }


    /**
     * Get missing translation keys for the specified locales.
     *
     * @param array $locales
     * @return array - array of missing translations keys grouped by locale
     */
    public function getMissingTranslationKeys(array $locales): array
    {
        return collect($locales)
            ->mapWithKeys(function ($locale) {
                $missing = collect(($this->collectMissingTranslationsAction)($locale));

                return [$locale => $missing->keys()->toArray()];
            })
            ->toArray();
    }

    /**
     * Get missing translation keys for the specified locale.
     *
     * @param string $locale
     * @return array - array of missing translations keys
     */
    public function getMissingTranslationKeysForLocale(string $locale): array
    {
        return collect(($this->collectMissingTranslationsAction)($locale))->keys()->toArray();
    }

    /**
     * Get the translation keys for the specified locale.
     *
     * @param string $locale
     * @return array
     */
    public function getTranslationKeys(string $locale): array
    {
        return collect(($this->collectTranslationsAction)($locale))->keys()->toArray();
    }
}
