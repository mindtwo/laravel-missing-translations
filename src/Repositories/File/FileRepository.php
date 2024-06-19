<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\File;

use Illuminate\Support\Facades\Lang;
use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;
use mindtwo\LaravelMissingTranslations\Repositories\File\Actions\CollectMissingTranslationsAction;
use mindtwo\LaravelMissingTranslations\Repositories\File\Actions\CollectTranslationsAction;

class FileRepository implements MissingTranslationRepository
{
    public function __construct(
        protected CollectMissingTranslationsAction $collectMissingTranslationsAction,
        protected CollectTranslationsAction $collectTranslationsAction,
    ) {}

    /**
     * Check if the given key exists.
     */
    public function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? Lang::getLocale();

        return Lang::has($key, $locale, false);
    }

    /**
     * Get the translation for the given key.
     */
    public function get(string $key, ?string $locale = null): ?string
    {
        $locale = $locale ?? Lang::getLocale();

        if (! $this->has($key, $locale)) {
            return null;
        }

        // Get the translation for the given key
        $value = Lang::get($key, [], $locale);

        // Return null if the value is an array
        if (is_array($value)) {
            return null;
        }

        return $value;
    }

    /**
     * Get missing translations for the specified locales.
     *
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
     */
    public function getMissingTranslationsForLocale(string $locale): array
    {
        return collect(($this->collectMissingTranslationsAction)($locale))->toArray();
    }

    /**
     * Get missing translation keys for the specified locales.
     *
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
     * @return array - array of missing translations keys
     */
    public function getMissingTranslationKeysForLocale(string $locale): array
    {
        return collect(($this->collectMissingTranslationsAction)($locale))->keys()->toArray();
    }

    /**
     * Get all translation keys for the specified locale.
     */
    public function getTranslationKeys(array $locales): array
    {
        return collect($locales)
            ->reduce(function ($carry, $locale) {
                return array_merge($carry, $this->getTranslationKeysForLocale($locale));
            }, []);
    }

    /**
     * Get the translation keys for the specified locale.
     */
    protected function getTranslationKeysForLocale(string $locale): array
    {
        return collect(($this->collectTranslationsAction)($locale))->keys()->toArray();
    }
}
