<?php

namespace mindtwo\LaravelMissingTranslations\Actions;

use Illuminate\Support\Arr;

class CollectTranslationsAction
{

    public function __invoke(
        string $locale,
    ): array
    {
        // Get the language files for the main locale
        $langFiles = $this->getLangFiles($locale);

        $translations = [];
        foreach ($langFiles as $file => $path) {
            $translations = array_merge(
                $translations,
                $this->getFileContent($path),
            );
        }

        return $translations;
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

        // Get the filename
        $filename = basename($file);
        if (str_ends_with($filename, '.php')) {
            return Arr::dot(require $file, str_replace('.php', '', $filename) . '.');
        }

        return json_decode(file_get_contents($file), true);
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
