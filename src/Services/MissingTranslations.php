<?php

namespace mindtwo\LaravelMissingTranslations\Services;

use InvalidArgumentException;
use mindtwo\LaravelMissingTranslations\Contracts\MissingTranslationRepository;

class MissingTranslations
{
    /**
     * Create a new missing translations service instance.
     *
     * @param  array<int, string>  $locales
     * @param  array<string, class-string<MissingTranslationRepository>>  $repositorySources
     */
    public function __construct(
        protected array $locales,
        protected string $mainLocale,
        protected array $repositorySources,
    ) {}

    /**
     * Resolve the repository instance by name.
     */
    public function repository(?string $name = null): MissingTranslationRepository
    {
        $name = $name ?? config('missing-translations.repositories.default');

        if (! array_key_exists($name, $this->repositorySources)) {
            throw new InvalidArgumentException("Missing translation repository [{$name}] not found.");
        }

        return app($this->repositorySources[$name]);
    }
}
