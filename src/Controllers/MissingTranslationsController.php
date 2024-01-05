<?php

namespace mindtwo\LaravelMissingTranslations\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class MissingTranslationsController extends Controller
{
    /**
     * Show missing translations
     *
     * @return View|Factory
     */
    public function show()
    {
        // Set default and available locales
        $locales_default = config('missing-translations.main_locale');
        $locales_available = config('missing-translations.locales');

        // Check all project translation files
        $lang_filenames_without_extension = $this->getLangFilenamesWithoutExtension($locales_available);

        // Set default translations
        $default = $this->getDefaultLanguageTranslations($lang_filenames_without_extension, $locales_default);
        $default_without_values = $this->cleanupValuesFromArray($default->toArray());

        // Create translations and render view
        return $this->renderShow(
            $this->getTranslations(
                $locales_available,
                $lang_filenames_without_extension,
                $default_without_values,
                $locales_default,
                $default
            )
        );
    }

    /**
     * Clear array recursively with an empty string
     */
    protected function cleanupValuesFromArray($array): Collection
    {
        return collect($array)->map(function ($value) {
            if (is_array($value)) {
                return $this->cleanupValuesFromArray($value);
            }

            return '';
        });
    }

    /**
     * @return mixed
     */
    protected function getTranslations($locales_available, $lang_filenames_without_extension, $default_without_values, $locales_default, $default)
    {
        return collect($locales_available)->mapWithKeys(function ($locale) {
            return [$locale => []];
        })->transform(function ($value, $lang_key) use ($lang_filenames_without_extension, $default_without_values) {
            $defaultJsLanguageFile = resource_path('lang/'.$lang_key.'.json');
            if (file_exists($defaultJsLanguageFile)) {
                $jsTranslations = json_decode(file_get_contents($defaultJsLanguageFile), true);
            } else {
                $jsTranslations = false;
            }

            return $default_without_values->merge(collect(Arr::dot($lang_filenames_without_extension->map(function ($output, $file) use ($lang_key) {
                if (app('translator')->has($file, $lang_key, false)) {
                    return trans($file, [], $lang_key);
                }
            })->reject(function ($value) {
                return is_null($value);
            })->when(($jsTranslations), function ($collection) use ($jsTranslations) {
                return $collection->merge(['js' => $jsTranslations]);
            })->toArray())));
        })->put($locales_default, $default);
    }

    protected function getDefaultLanguageTranslations($lang_filenames_without_extension, $locales_default): Collection
    {
        $defaultJsLanguageFile = resource_path('lang/'.$locales_default.'.json');
        if (file_exists($defaultJsLanguageFile)) {
            $jsTranslations = json_decode(file_get_contents($defaultJsLanguageFile), true);
        } else {
            $jsTranslations = false;
        }

        return collect(Arr::dot($lang_filenames_without_extension->map(function ($output, $file) use ($locales_default) {
            return trans($file, [], $locales_default);
        })->when($jsTranslations ?? false, function ($collection) use ($jsTranslations) {
            return $collection->merge(['js' => $jsTranslations]);
        })->toArray()));
    }

    /**
     * @return mixed
     */
    protected function getLangFilenamesWithoutExtension($locales_available)
    {
        return collect($locales_available)->map(function ($locale) {
            $folder = App::langPath().'/'.$locale;

            return glob("{$folder}/*.php");
        })->flatten()->map(function ($full_path) {
            return str_replace('.php', '', basename($full_path));
        })->unique()->mapWithKeys(function ($filename_without_extension) {
            return [$filename_without_extension => []];
        })->sortKeys();
    }

    /**
     * @return View|Factory
     */
    protected function renderShow($translations): \Illuminate\View\View
    {
        return view('missing-translations::index', [
            'translations' => $translations,
        ]);
    }
}
