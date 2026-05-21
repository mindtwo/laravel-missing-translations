<?php

namespace mindtwo\LaravelMissingTranslations\Commands;

use Illuminate\Console\Command;
use mindtwo\LaravelMissingTranslations\Models\MissingTranslation;
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;

class CollectMissingTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'm2:collect-missing-translations
        {--L|locales=* : The locales to collect the missing translations for}
        {--dry-run : Perform a dry run without persisting any results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect missing translations for the given locales.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $locales = ! empty($this->option('locales'))
            ? $this->option('locales')
            : config('missing-translations.locales');

        if (empty($locales)) {
            $this->error('No locales passed or specified in config/missing-translations.');

            return Command::FAILURE;
        }

        $this->collectLangFileDiff($locales);

        return Command::SUCCESS;
    }

    /**
     * Collect and persist the missing translations for each locale.
     *
     * @param  array<int, string>  $locales
     */
    protected function collectLangFileDiff(array $locales): void
    {
        $mainLocale = config('missing-translations.main_locale');

        foreach ($locales as $locale) {
            $locale = str_replace('=', '', $locale);

            if ($locale === $mainLocale) {
                continue;
            }

            $diff = app(MissingTranslations::class)->repository()->getMissingTranslationsForLocale($locale);

            if (empty($diff)) {
                $this->info("No missing translations found for locale '{$locale}'.");

                continue;
            }

            $this->info('Found '.count($diff)." missing translations for locale '{$locale}'.");

            if ($this->option('dry-run')) {
                continue;
            }

            collect($diff)->each(function (string $value, string $key) use ($locale) {
                MissingTranslation::firstOrCreate(
                    ['hash' => md5($key)],
                    ['string' => $key, 'locale' => $locale],
                );
            });
        }
    }
}
