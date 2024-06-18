<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\File\Actions;

use Illuminate\Support\Arr;

class CollectMissingTranslationsAction
{

    public function __invoke(
        string $locale,
        ?string $mainLocale = null,
    ): array
    {
        // Get the main locale
        $mainLocale = $mainLocale ?? config('missing-translations.main_locale');

        return $this->getDiffForLocale($mainLocale, $locale);
    }

    /**
     * Collect the missing translations for the specified locale.
     */
    protected function getDiffForLocale(string $mainLocale, string $comparedLocale): array
    {
        $diff = [];

        $mainLocaleFiles = $this->getLangFiles($mainLocale);

        // Get the language files for the main locale
        $langFiles = $this->getLangFiles($comparedLocale);

        foreach ($mainLocaleFiles as $file => $path) {
            $mainContent = $this->getFileContent($path);

            // json base is named after the locale
            $file = str_ends_with($file, '.json') ? str_replace($mainLocale, $comparedLocale, $file) : $file;

            $langContent = !isset($langFiles[$file]) ? [] : $this->getFileContent($langFiles[$file]);

            $diff = array_merge($diff, $this->getArrayDiffDotted($mainContent, $langContent, $file));
        }

        return $diff;
    }

    /**
     * Get the difference between two arrays.
     *
     * @param array $array1
     * @param array $array2
     * @param string $file
     *
     * @return array
     */
    protected function getArrayDiffDotted(array $array1, array $array2, string $file = ''): array
    {
        if (str_ends_with($file, '.php')) {
            $file = str_replace(['.php'], '', $file) . '.';
        } else {
            $file = '';
        }

        $array1 = Arr::dot($array1, $file);
        $array2 = Arr::dot($array2, $file);

        return array_diff_key($array1, $array2);
    }

    /**
     * Get the content of the specified file.
     *
     * @param string $file
     *
     * @return array
     */
    protected function getFileContent(string $file): array
    {
        if (! file_exists($file)) {
            return [];
        }

        return str_ends_with($file, '.json') ? json_decode(file_get_contents($file), true) : require $file;
    }

    /**
     * Get the language files for the specified locale.
     *
     * @param string $locale
     *
     * @return array
     */
    protected function getLangFiles(string $locale): array
    {
        $files = [];

        $basePath = base_path("lang/$locale");
        if (is_dir($basePath)) {
            $files = scandir($basePath);
        }

        if (file_exists(base_path("lang/$locale.json"))) {
            $files[] = base_path("lang/$locale.json");
        }

        return collect($files)
            ->filter(function ($file) {
                return ! in_array($file, ['.', '..']);
            })
            ->mapWithKeys(function ($file) use ($basePath) {
                if (str_starts_with($file, base_path())) {
                    return [str_replace(base_path('lang/'), '', $file) => $file];
                }

                return [$file => $basePath . '/' . $file];
            })
            ->toArray();
    }
}
