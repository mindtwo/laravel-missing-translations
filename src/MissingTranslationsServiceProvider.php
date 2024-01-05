<?php

namespace mindtwo\LaravelMissingTranslations;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use mindtwo\LaravelMissingTranslations\Models\MissingTranslation;
use Throwable;

class MissingTranslationsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/missing-translations.php', 'missing-translations'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'missing-translations');

        $this->publishes([
            __DIR__.'/../config/missing-translations.php' => config_path('missing-translations.php'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/missing-translations'),
        ]);

        if (config('missing-translations.log_missing_keys')) {
            Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
                try {
                    MissingTranslation::firstOrCreate([
                        'hash' => md5($key),
                    ], [
                        'string' => $key,
                        'locale' => $locale,
                    ]);
                } catch (Throwable $e) {
                    Log::error($e->getMessage());
                }

                return $key;
            });
        }
    }
}
