<?php

namespace mindtwo\LaravelMissingTranslations\Services;

use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;

class MissingTranslations
{
    public function __construct(
        protected array $locales,
        protected string $mainLocale,
        protected array $repositories,
    ) {}

    public function repo(?string $name = null): MissingTranslationRepository
    {
        $name = $name ?? config('missing-translations.repository');

        if (! array_key_exists($name, $this->repositories)) {
            throw new \InvalidArgumentException("Missing translation repository [$name] not found.");
        }

        return app($this->repositories[$name]);
    }
}
