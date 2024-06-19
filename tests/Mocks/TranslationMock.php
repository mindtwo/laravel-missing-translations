<?php
namespace mindtwo\LaravelMissingTranslations\Tests\Mocks;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;

class TranslationMock implements Translator
{

    private string $locale = 'en';

    protected $missingTranslationKeyCallback;

    public function __construct(
        protected array $translations = []
    )
    {
    }

    public function trans($key, array $replace = [], $locale = null)
    {
        // Return the key itself for simplicity, or customize as needed.
        return $key;
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->getLocale();

        if (! isset($this->translations[$locale])) {
            return $key;
        }

        $translations = $this->translations[$locale];

        $translation = Arr::get($translations, $key);

        if (! is_null($translation)) {
            return $translation;
        }

        if ($this->missingTranslationKeyCallback) {
            return call_user_func($this->missingTranslationKeyCallback, $key, $locale);
        }

        return $key;
    }

    public function choice($key, $number, array $replace = [], $locale = null)
    {
        return $key;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function addTranslations(string $localeCode, array $lines)
    {
        if (! isset($this->translations[$localeCode])) {
            $this->translations[$localeCode] = [];
        }

        $this->translations[$localeCode] = array_merge($this->translations[$localeCode], $lines);
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Register a callback that is responsible for handling missing translation keys.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function handleMissingKeysUsing(?callable $callback)
    {
        $this->missingTranslationKeyCallback = $callback;

        return $this;
    }
}
