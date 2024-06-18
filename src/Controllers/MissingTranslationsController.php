<?php

namespace mindtwo\LaravelMissingTranslations\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use mindtwo\LaravelMissingTranslations\Actions\CollectMissingTranslationsAction;
use mindtwo\LaravelMissingTranslations\Actions\CollectTranslationsAction;

class MissingTranslationsController extends Controller
{
    /**
     * Show missing translations
     *
     * @return View|Factory
     */
    public function show(Request $request)
    {
        // Set default and available locales
        $locales_default = config('missing-translations.main_locale');
        $locales_available = $this->getLocales($request);

        // Collect all missing translations
        $checkedLanguageKeys = $this->getLanguageKeys($request, $locales_default, $locales_available);

        // Create translations and render view
        return $this->renderShow(
            $this->getTranslationTable(
                $locales_available,
                $locales_default,
                $checkedLanguageKeys,
            )
        );
    }

    /**
     * @return mixed
     */
    protected function getTranslationTable(array $locales, string $defaultLocale, Collection $languageKeys)
    {
        // Add the default locale to the list of available locales
        $avaiableLocales = [
            $defaultLocale,
            ...$locales,
        ];

        // Create the table rows
        $rows = $languageKeys
                    ->transform(function ($translationKey) use ($avaiableLocales) {
                        $row = [
                            substr(md5($translationKey), 0, 6),
                            $translationKey,
                        ];

                        // Check if the translation key from the default locale is missing in the other locales
                        foreach ($avaiableLocales as $locale) {
                            $row[] = Lang::has($translationKey, $locale, false) ? Lang::get($translationKey, [], $locale) : null;
                        }

                        return $row;
                    })->reject(function ($value) {
                        return is_null($value);
                    });

        return [
            'header' => [
                'Hash',
                'Language File and Key',
                "Default Language ($defaultLocale)",
                // Add the other locales as headers
                ...Arr::flatten(
                    collect($locales)
                        ->map(fn ($locale) => ["Language $locale"])
                        ->toArray()
                ),
            ],
            'rows' => $rows->toArray(),
        ];
    }

    /**
     * Get all language keys
     *
     * @return Collection
     */
    protected function getLanguageKeys(Request $request, string $defaultLocale, array $locales): Collection
    {
        $onlyMissing = $request->has('only_missing');

        if (! $onlyMissing) {
            return collect(app(CollectTranslationsAction::class)($defaultLocale))->keys();
        }

        $collectAction = app(CollectMissingTranslationsAction::class);

        // Collect all missing translation keys
        $keys = collect([]);
        foreach ($locales as $value) {
            $missing = $collectAction($value, $defaultLocale);

            $keys = $keys->merge(array_keys($missing));
        }

        // Collect all missing translations
        return $keys->unique();
    }

    /**
     * Get the locales to collect the missing translations for
     *
     * @return array
     */
    protected function getLocales(Request $request): array
    {
        $avaiableLocales = config('missing-translations.locales');

        if (! $request->has('exclude')) {
            return $avaiableLocales;
        }

        return array_filter($avaiableLocales, function ($locale) use ($request) {
            return ! in_array($locale, $request->get('exclude'));
        });
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    protected function renderShow(array $table): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('missing-translations::index', [
            'table' => $table,
        ]);
    }
}
