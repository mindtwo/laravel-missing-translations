<?php

namespace mindtwo\LaravelMissingTranslations\Contracts;

interface MissingTranslationRepository
{
    /**
     * Determine if a translation exists for the given key.
     */
    public function has(string $key, ?string $locale = null): bool;

    /**
     * Retrieve the translation for the given key.
     */
    public function get(string $key, ?string $locale = null): ?string;

    /**
     * Get the missing translations grouped by the given locales.
     *
     * @param  array<int, string>  $locales
     * @return array<string, array<string, string>>
     */
    public function getMissingTranslations(array $locales): array;

    /**
     * Get the missing translations for the given locale.
     *
     * @return array<string, string>
     */
    public function getMissingTranslationsForLocale(string $locale): array;

    /**
     * Get the missing translation keys grouped by the given locales.
     *
     * @param  array<int, string>  $locales
     * @return array<string, array<int, string>>
     */
    public function getMissingTranslationKeys(array $locales): array;

    /**
     * Get the missing translation keys for the given locale.
     *
     * @return array<int, string>
     */
    public function getMissingTranslationKeysForLocale(string $locale): array;

    /**
     * Get every translation key defined across the given locales.
     *
     * @param  array<int, string>  $locales
     * @return array<int, string>
     */
    public function getTranslationKeys(array $locales): array;
}
