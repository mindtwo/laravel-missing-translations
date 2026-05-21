<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\File;

use Illuminate\Support\Facades\Lang;
use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;
use mindtwo\LaravelMissingTranslations\Repositories\File\Actions\CollectMissingTranslationsAction;
use mindtwo\LaravelMissingTranslations\Repositories\File\Actions\CollectTranslationsAction;

class FileRepository implements MissingTranslationRepository
{
    /**
     * Create a new file repository instance.
     */
    public function __construct(
        protected CollectMissingTranslationsAction $collectMissingTranslationsAction,
        protected CollectTranslationsAction $collectTranslationsAction,
    ) {}

    /**
     * Determine if a translation exists for the given key.
     */
    public function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? Lang::getLocale();

        return Lang::has($key, $locale, false);
    }

    /**
     * Retrieve the translation for the given key.
     */
    public function get(string $key, ?string $locale = null): ?string
    {
        $locale = $locale ?? Lang::getLocale();

        if (! $this->has($key, $locale)) {
            return null;
        }

        $value = Lang::get($key, [], $locale);

        if (is_array($value)) {
            return null;
        }

        return $value;
    }

    /**
     * Get the missing translations grouped by the given locales.
     *
     * @param  array<int, string>  $locales
     * @return array<string, array<string, string>>
     */
    public function getMissingTranslations(array $locales): array
    {
        return collect($locales)
            ->mapWithKeys(fn (string $locale) => [
                $locale => ($this->collectMissingTranslationsAction)($locale),
            ])
            ->all();
    }

    /**
     * Get the missing translations for the given locale.
     *
     * @return array<string, string>
     */
    public function getMissingTranslationsForLocale(string $locale): array
    {
        return ($this->collectMissingTranslationsAction)($locale);
    }

    /**
     * Get the missing translation keys grouped by the given locales.
     *
     * @param  array<int, string>  $locales
     * @return array<string, array<int, string>>
     */
    public function getMissingTranslationKeys(array $locales): array
    {
        return collect($locales)
            ->mapWithKeys(fn (string $locale) => [
                $locale => array_keys(($this->collectMissingTranslationsAction)($locale)),
            ])
            ->all();
    }

    /**
     * Get the missing translation keys for the given locale.
     *
     * @return array<int, string>
     */
    public function getMissingTranslationKeysForLocale(string $locale): array
    {
        return array_keys(($this->collectMissingTranslationsAction)($locale));
    }

    /**
     * Get every translation key defined across the given locales.
     *
     * @param  array<int, string>  $locales
     * @return array<int, string>
     */
    public function getTranslationKeys(array $locales): array
    {
        return collect($locales)
            ->reduce(
                fn (array $carry, string $locale) => array_merge($carry, $this->getTranslationKeysForLocale($locale)),
                [],
            );
    }

    /**
     * Get every translation key defined for the given locale.
     *
     * @return array<int, string>
     */
    protected function getTranslationKeysForLocale(string $locale): array
    {
        return array_keys(($this->collectTranslationsAction)($locale));
    }
}
