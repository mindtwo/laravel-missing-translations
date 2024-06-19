<?php

namespace mindtwo\LaravelMissingTranslations\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;

class MissingTranslationsController extends Controller
{
    public function __construct(
        protected MissingTranslations $missingTranslations,
    ) {
        // Pause the logging of missing keys
        config()->set('missing-translations.log_paused', true);

        // Set the authorization middleware
        if ($gate = config('missing-translations.authorization.gate')) {
            $gate = is_bool($gate) ? 'viewMissingTranslations' : $gate;

            $this->middleware("can:$gate");
        }
    }

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
     */
    protected function getLanguageKeys(Request $request, string $defaultLocale, array $locales): Collection
    {
        $onlyMissing = $request->has('only_missing');
        $repository = $this->missingTranslations->repo();

        // Get the missing translation keys
        if ($onlyMissing) {
            return collect($repository->getMissingTranslationKeys($locales))->flatten();
        }

        return collect($repository->getTranslationKeys($defaultLocale));
    }

    /**
     * Get the locales to collect the missing translations for
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

    protected function renderShow(array $table): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        config()->set('missing-translations.log_missing_keys', true);

        return view('missing-translations::index', [
            'table' => $table,
        ]);
    }
}
