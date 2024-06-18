<?php

namespace mindtwo\LaravelMissingTranslations\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use mindtwo\LaravelMissingTranslations\Actions\CollectMissingTranslationsAction;
use mindtwo\LaravelMissingTranslations\Models\MissingTranslation;

class CollectMissingTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'm2:collect-missing-translations
        {--L|locales=* : The locales to collect the missing translations for}
        {--dry-run : Perform a dry run without collecting any missing translations}
    ';
        // {--F|force : Force the collection without asking for confirmation}

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect missing translations for the specified locales.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get the locales to collect the missing translations for
        $locales = ! empty($this->option('locales')) ? $this->option('locales') : config('missing-translations.locales');

        if (empty($locales)) {
            $this->error('No locales passed ord specified in config/missing-translations.');

            return Command::FAILURE;
        }

        // Get the language key diff
        $this->collectLangFileDiff($locales);

        return Command::SUCCESS;
    }

    /**
     * Collect the missing translations for the specified locales.
     *
     * @param array $locales
     */
    protected function collectLangFileDiff(array $locales): void
    {
        // Get the main locale
        $mainLocale = config('missing-translations.main_locale');

        foreach ($locales as $locale) {
            $locale = str_replace('=','',$locale);

            // Skip the main locale
            if ($locale === $mainLocale) {
                continue;
            }

            $diff = app(CollectMissingTranslationsAction::class)($locale, $mainLocale);

            if (empty($diff)) {
                $this->info("No missing translations found for locale '$locale'.");

                continue;
            }

            $this->info("Found " . count($diff) . " missing translations for locale '$locale'.");

            if ($this->option('dry-run')) {
                continue;
            }

            collect($diff)->each(function ($value, $key) use ($locale) {
                // Save the missing translations
                MissingTranslation::firstOrCreate([
                    'hash' => md5($key),
                ], [
                    'string' => $key,
                    'locale' => $locale,
                ]);
            });
        }

    }


}
