<?php

namespace mindtwo\LaravelMissingTranslations\Contracts;

interface MissingTranslationRepository
{
    /**
     * Check if the given key exists.
     */
    public function has(string $key, ?string $locale = null): bool;

    /**
     * Get the translation for the given key.
     */
    public function get(string $key, ?string $locale = null): ?string;

    /**
     * Get missing translations for the specified locales.
     *
     * @return array - array of missing translations with key grouped by locale
     */
    public function getMissingTranslations(array $locales): array;

    /**
     * Get missing translations for the specified locale.
     *
     * @return array - array of missing translations with key
     */
    public function getMissingTranslationsForLocale(string $locale): array;

    /**
     * Get missing translation keys for the specified locales.
     *
     * @return array - array of missing translations keys grouped by locale
     */
    public function getMissingTranslationKeys(array $locales): array;

    /**
     * Get missing translation keys for the specified locale.
     *
     * @return array - array of missing translations keys
     */
    public function getMissingTranslationKeysForLocale(string $locale): array;

    /**
     * Get the translation keys for the specified locale.
     *
     * @return array - array of translation keys for all locales
     */
    public function getTranslationKeys(array $locale): array;

    /**
     * Get the translation keys for the specified locale.
     *
     * @return array - array of translation keys
     */
    public function getTranslationKeysForLocale(string $locale): array;
}
