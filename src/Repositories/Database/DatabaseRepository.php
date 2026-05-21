<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\Database;

use Illuminate\Support\Facades\Lang;
use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;
use mindtwo\LaravelMissingTranslations\Models\MissingTranslation;
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;

class DatabaseRepository implements MissingTranslationRepository
{
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
        return MissingTranslation::whereIn('locale', $locales)
            ->get()
            ->groupBy('locale')
            ->mapWithKeys(fn ($items, $locale) => [
                $locale => $items->reduce(function (array $carry, MissingTranslation $item) {
                    $carry[$item->string] = '';

                    return $carry;
                }, []),
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
        return collect($this->getMissingTranslationKeysForLocale($locale))
            ->mapWithKeys(fn (string $key) => [$key => ''])
            ->all();
    }

    /**
     * Get the missing translation keys grouped by the given locales.
     *
     * @param  array<int, string>  $locales
     * @return array<string, array<int, string>>
     */
    public function getMissingTranslationKeys(array $locales): array
    {
        return MissingTranslation::whereIn('locale', $locales)
            ->get()
            ->reduce(function (array $carry, MissingTranslation $item) {
                $carry[$item->locale][] = $item->string;

                return $carry;
            }, []);
    }

    /**
     * Get the missing translation keys for the given locale.
     *
     * @return array<int, string>
     */
    public function getMissingTranslationKeysForLocale(string $locale): array
    {
        return MissingTranslation::where('locale', $locale)
            ->pluck('string')
            ->all();
    }

    /**
     * Get every translation key defined across the given locales.
     *
     * Delegates to the file repository since the database only tracks misses.
     *
     * @param  array<int, string>  $locales
     * @return array<int, string>
     */
    public function getTranslationKeys(array $locales): array
    {
        return app(MissingTranslations::class)->repository('file')->getTranslationKeys($locales);
    }
}
