<?php

namespace mindtwo\LaravelMissingTranslations\Services;

use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;

class MissingTranslations
{
    public function __construct(
        protected array $locales,
        protected string $mainLocale,
        protected array $repositorySources,
    ) {}

    public function repository(?string $name = null): MissingTranslationRepository
    {
        $name = $name ?? config('missing-translations.repositories.default');

        if (! array_key_exists($name, $this->repositorySources)) {
            throw new \InvalidArgumentException("Missing translation repository [$name] not found.");
        }

        return app($this->repositorySources[$name]);
    }
}
