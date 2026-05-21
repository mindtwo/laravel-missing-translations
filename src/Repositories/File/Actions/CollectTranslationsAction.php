<?php

namespace mindtwo\LaravelMissingTranslations\Repositories\File\Actions;

use Illuminate\Support\Arr;

class CollectTranslationsAction
{
    /**
     * Collect every translation defined for the given locale.
     *
     * @return array<string, mixed>
     */
    public function __invoke(string $locale): array
    {
        $translations = [];

        foreach ($this->getLangFiles($locale) as $path) {
            $translations = array_merge($translations, $this->getFileContent($path));
        }

        return $translations;
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

        $filename = basename($file);

        if (str_ends_with($filename, '.php')) {
            return Arr::dot(require $file, str_replace('.php', '', $filename).'.');
        }

        $contents = file_get_contents($file);

        return $contents === false ? [] : (json_decode($contents, true) ?? []);
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
