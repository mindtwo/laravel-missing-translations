<?php

namespace mindtwo\LaravelMissingTranslations\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;

class MissingTranslationsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected MissingTranslations $missingTranslations,
    ) {
        // Avoid logging missing keys while rendering this view.
        config()->set('missing-translations.log_paused', true);

        if ($gate = config('missing-translations.authorization.gate')) {
            $gate = is_bool($gate) ? 'viewMissingTranslations' : $gate;

            $this->middleware("can:{$gate}");
        }
    }

    /**
     * Show the missing translations table.
     */
    public function show(Request $request): View
    {
        $defaultLocale = config('missing-translations.main_locale');
        $availableLocales = $this->getLocales($request);

        $languageKeys = $this->getLanguageKeys($request, $availableLocales);

        return $this->renderShow(
            $this->getTranslationTable($availableLocales, $defaultLocale, $languageKeys),
            $availableLocales,
        );
    }

    /**
     * Build the translation table rows for the given locales and keys.
     *
     * @param  array<int, string>  $locales
     * @param  Collection<int, string>  $languageKeys
     * @return array{header: array<int, string>, rows: array<int, array<int, string|null>>}
     */
    protected function getTranslationTable(array $locales, string $defaultLocale, Collection $languageKeys): array
    {
        $availableLocales = [$defaultLocale, ...$locales];

        $repository = $this->repository();

        $rows = $languageKeys
            ->map(function (string $translationKey) use ($availableLocales, $repository) {
                $row = [
                    substr(md5($translationKey), 0, 6),
                    $translationKey,
                ];

                foreach ($availableLocales as $locale) {
                    $row[] = $repository->has($translationKey, $locale) ? $repository->get($translationKey, $locale) : null;
                }

                return $row;
            })
            ->values()
            ->all();

        return [
            'header' => [
                'Hash',
                'Language File and Key',
                "Default Language ({$defaultLocale})",
                ...Arr::flatten(
                    collect($locales)
                        ->map(fn (string $locale) => ["Language {$locale}"])
                        ->all()
                ),
            ],
            'rows' => $rows,
        ];
    }

    /**
     * Get the translation keys to display.
     *
     * @param  array<int, string>  $locales
     * @return Collection<int, string>
     */
    protected function getLanguageKeys(Request $request, array $locales): Collection
    {
        $repository = $this->repository();

        if ($request->has('only_missing')) {
            return collect($repository->getMissingTranslationKeys($locales))->flatten();
        }

        return collect($repository->getTranslationKeys($locales));
    }

    /**
     * Get the locales to display, applying the "exclude" filter from the request.
     *
     * @return array<int, string>
     */
    protected function getLocales(Request $request): array
    {
        $availableLocales = config('missing-translations.locales');

        if (! $request->has('exclude')) {
            return $availableLocales;
        }

        $excluded = (array) $request->input('exclude', []);

        return array_values(array_filter($availableLocales, fn (string $locale) => ! in_array($locale, $excluded)));
    }

    /**
     * Render the missing translations view.
     *
     * @param  array{header: array<int, string>, rows: array<int, array<int, string|null>>}  $table
     * @param  array<int, string>  $availableLocales
     */
    protected function renderShow(array $table, array $availableLocales): View
    {
        config()->set('missing-translations.log_missing_keys', true);

        return ViewFactory::make('missing-translations::index', [
            'table' => $table,
            'locales' => $availableLocales,
            'excluded' => (array) request()->input('exclude', []),
        ]);
    }

    /**
     * Resolve the repository selected by the current request.
     */
    private function repository(): MissingTranslationRepository
    {
        $repoName = (string) request()->input('repo', config('missing-translations.repositories.default', 'file'));

        if (! array_key_exists($repoName, config('missing-translations.repositories.sources'))) {
            abort(404, 'Repository not found');
        }

        return $this->missingTranslations->repository($repoName);
    }
}
