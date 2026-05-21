<?php

namespace mindtwo\LaravelMissingTranslations;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use mindtwo\LaravelMissingTranslations\Commands\CollectMissingTranslationsCommand;
use mindtwo\LaravelMissingTranslations\Models\MissingTranslation;
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;
use Throwable;

class MissingTranslationsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/missing-translations.php', 'missing-translations');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadResources();
        $this->registerPublishing();

        $this->commands([
            CollectMissingTranslationsCommand::class,
        ]);

        $this->app->singleton(MissingTranslations::class, fn () => new MissingTranslations(
            locales: config('missing-translations.locales'),
            mainLocale: config('missing-translations.main_locale'),
            repositorySources: config('missing-translations.repositories.sources'),
        ));

        if (config('missing-translations.log_missing_keys')) {
            $this->registerMissingKeyHandler();
        }
    }

    /**
     * Register the missing translation key handler with the translator.
     */
    protected function registerMissingKeyHandler(): void
    {
        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            if (config('missing-translations.log_paused')) {
                return $key;
            }

            try {
                MissingTranslation::firstOrCreate(
                    ['hash' => md5($key)],
                    ['string' => $key, 'locale' => $locale],
                );
            } catch (Throwable $e) {
                Log::error($e->getMessage());
            }

            return $key;
        });
    }

    /**
     * Load the package migrations, routes, and views.
     */
    protected function loadResources(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->environment(config('missing-translations.allowed_environments'))) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'missing-translations');
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/missing-translations.php' => config_path('missing-translations.php'),
        ], 'missing-translations-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/missing-translations'),
        ], 'missing-translations-views');
    }
}
