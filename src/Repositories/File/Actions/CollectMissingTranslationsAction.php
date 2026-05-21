<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\File\Actions;

use Illuminate\Support\Arr;

class CollectMissingTranslationsAction
{
    /**
     * Collect the missing translations for the given locale.
     *
     * @return array<string, string>
     */
    public function __invoke(string $locale, ?string $mainLocale = null): array
    {
        $mainLocale = $mainLocale ?? config('missing-translations.main_locale');

        return $this->getDiffForLocale($mainLocale, $locale);
    }

    /**
     * Compare each language file in the main locale against the given locale.
     *
     * @return array<string, string>
     */
    protected function getDiffForLocale(string $mainLocale, string $comparedLocale): array
    {
        $diff = [];

        $mainLocaleFiles = $this->getLangFiles($mainLocale);
        $comparedLocaleFiles = $this->getLangFiles($comparedLocale);

        foreach ($mainLocaleFiles as $file => $path) {
            $mainContent = $this->getFileContent($path);

            // JSON translation files are named after the locale, so adjust the
            // lookup key to find the matching file in the compared locale.
            $file = str_ends_with($file, '.json') ? str_replace($mainLocale, $comparedLocale, $file) : $file;

            $langContent = isset($comparedLocaleFiles[$file]) ? $this->getFileContent($comparedLocaleFiles[$file]) : [];

            $diff = array_merge($diff, $this->getArrayDiffDotted($mainContent, $langContent, $file));
        }

        return $diff;
    }

    /**
     * Get the dotted-key diff between two translation arrays.
     *
     * @param  array<string, mixed>  $array1
     * @param  array<string, mixed>  $array2
     * @return array<string, mixed>
     */
    protected function getArrayDiffDotted(array $array1, array $array2, string $file = ''): array
    {
        $prefix = str_ends_with($file, '.php') ? str_replace('.php', '', $file).'.' : '';

        $array1 = Arr::dot($array1, $prefix);
        $array2 = Arr::dot($array2, $prefix);

        return array_diff_key(
            $array1 + $array2,
            array_intersect_key($array1, $array2),
        );
    }

    /**
     * Read and decode the contents of the given translation file.
     *
     * @return array<string, mixed>
     */
    protected function getFileContent(string $file): array
    {
        if (! file_exists($file)) {
            return [];
        }

        if (str_ends_with($file, '.json')) {
            $contents = file_get_contents($file);

            return $contents === false ? [] : (json_decode($contents, true) ?? []);
        }

        return require $file;
    }

    /**
     * Get the language files for the given locale, keyed by their relative path.
     *
     * @return array<string, string>
     */
    protected function getLangFiles(string $locale): array
    {
        $files = [];

        $basePath = lang_path($locale);
        if (is_dir($basePath)) {
            $files = scandir($basePath) ?: [];
        }

        if (file_exists(lang_path("{$locale}.json"))) {
            $files[] = lang_path("{$locale}.json");
        }

        return collect($files)
            ->filter(fn (string $file) => ! in_array($file, ['.', '..']))
            ->mapWithKeys(function (string $file) use ($basePath) {
                if (str_starts_with($file, lang_path())) {
                    return [str_replace(lang_path(), '', $file) => $file];
                }

                return [$file => $basePath.'/'.$file];
            })
            ->all();
    }
}
