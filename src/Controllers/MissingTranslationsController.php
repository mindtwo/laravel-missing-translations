<?php

namespace mindtwo\LaravelMissingTranslations\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;

class MissingTranslationsController extends BaseController
{
    public function show()
    {
        // Set default and available locales
        $localesDefault = config('missing-translations.main_locale');
        $localesAvailable = config('missing-translations.locales');

        // Check all project translation files
        $lang_filenames_without_extention = $this->getLangFilenamesWithoutExtention($localesAvailable);

        // Set default translations
        $default = $this->getDefaultLanguageTranslations($lang_filenames_without_extention, $localesDefault);
        $default_without_values = $this->cleanupValuesFromArray($default->toArray());

        // Create translations and render view
        return $this->renderShow(
            $this->getTranslations(
                $localesAvailable,
                $lang_filenames_without_extention,
                $default_without_values,
                $localesDefault,
                $default
            )
        );
    }

    /**
     * @param $array
     *
     * @return array
     */
    protected function cleanupValuesFromArray($array)
    {
        return collect($array)->map(function ($value) {
            if (is_array($value)) {
                return $this->cleanupValuesFromArray($value);
            }

            return '';
        });
    }

    /**
     * @param $locales_available
     * @param $lang_filenames_without_extention
     * @param $default_without_values
     * @param $locales_default
     * @param $default
     *
     * @return mixed
     */
    protected function getTranslations($locales_available, $lang_filenames_without_extention, $default_without_values, $locales_default, $default)
    {
        return collect($locales_available)->mapWithKeys(function ($locale) {
            return [$locale => []];
        })->transform(function ($value, $lang_key) use ($lang_filenames_without_extention, $default_without_values) {
            $defaultJsLanguageFile = resource_path('lang/'.$lang_key.'.json');
            if(file_exists($defaultJsLanguageFile)) {
                $jsTranslations = json_decode(file_get_contents($defaultJsLanguageFile), true);
            } else {
                $jsTranslations = false;
            }

            return $default_without_values->merge(collect(array_dot($lang_filenames_without_extention->map(function ($output, $file) use ($lang_key) {
                if (app('translator')->has($file, $lang_key, false)) {
                    return trans($file, [], $lang_key);
                }
            })->reject(function ($value) {
                return is_null($value);
            })->when(($jsTranslations), function($collection) use($jsTranslations){
                return $collection->merge(['js' => $jsTranslations]);
            })->toArray())));
        })->put($locales_default, $default)->reverse();
    }

    /**
     * @param $lang_filenames_without_extention
     * @param $locales_default
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getDefaultLanguageTranslations($lang_filenames_without_extention, $locales_default): \Illuminate\Support\Collection
    {
        $defaultJsLanguageFile = resource_path('lang/'.$locales_default.'.json');
        if(file_exists($defaultJsLanguageFile)) {
            $jsTranslations = json_decode(file_get_contents($defaultJsLanguageFile), true);
        }
        return collect(Arr::dot($lang_filenames_without_extention->map(function ($output, $file) use ($locales_default) {
            return trans($file, [], $locales_default);
        })->when($jsTranslations ?? false, function($collection) use($jsTranslations){
            return $collection->merge(['js' => $jsTranslations]);
        })->toArray()));
    }

    /**
     * @param $locales_available
     *
     * @return mixed
     */
    protected function getLangFilenamesWithoutExtention($locales_available)
    {
        return collect($locales_available)->map(function ($locale) {
            $folder = base_path('resources/lang/'.$locale);

            return glob("{$folder}/*.php");
        })->flatten()->map(function ($full_path) {
            return str_replace('.php', '', basename($full_path));
        })->unique()->mapWithKeys(function ($filename_without_extention) {
            return [$filename_without_extention => []];
        })->sortKeys();
    }

    /**
     * @param $translations
     *
     * @return string
     */
    protected function renderShow($translations)
    {
        ob_start();
        echo '<html>';
        echo '<head>';
        echo '<meta name="robots" content="noindex, nofollow">';
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">';
        echo '</head>';
        echo '<body class="p-5">';
        echo '<table class="table table-condensed table-striped table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Language File and Key</th>';
        foreach ($translations as $language => $values) {
            echo '<th>Language '.strtoupper($language).'</th>';
        }
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        $i = 0;
        foreach ($translations->first() as $key => $value) {
            echo '<tr>';
            echo '<td>'.$i++.'</td>';
            echo '<td>'.$key.'</td>';
            foreach ($translations as $language => $values) {
                $values = collect($values);
                $style = (empty($values->get($key)) ? ' class="bg-danger"' : '');
                echo "<td $style>".$values->get($key).'</td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        ob_end_flush();
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }
}
