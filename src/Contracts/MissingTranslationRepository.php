<?php

namespace mindtwo\LaravelMissingTranslations\Contracts;

interface MissingTranslationRepository
{

    /**
     * Get missing translations for the specified locales.
     *
     * @param array $locales
     * @return array - array of missing translations with key grouped by locale
     */
    public function getMissingTranslations(array $locales): array;


    /**
     * Get missing translations for the specified locale.
     *
     * @param string $locale
     * @return array - array of missing translations with key
     */
    public function getMissingTranslationsForLocale(string $locale): array;

    /**
     * Get missing translation keys for the specified locales.
     *
     * @param array $locales
     * @return array - array of missing translations keys grouped by locale
     */
    public function getMissingTranslationKeys(array $locales): array;

    /**
     * Get missing translation keys for the specified locale.
     *
     * @param string $locale
     * @return array - array of missing translations keys
     */
    public function getMissingTranslationKeysForLocale(string $locale): array;

    /**
     * Get the translation keys for the specified locale.
     *
     * @param string $locale
     * @return array - array of translation keys
     */
    public function getTranslationKeys(string $locale): array;

}
