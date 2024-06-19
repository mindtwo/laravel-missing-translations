<?php

namespace mindtwo\LaravelMissingTranslations\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'mindtwo\\LaravelTranslatableServiceProvider\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        // Clear the route cache
        $this->artisan('route:clear');
        $this->artisan('config:clear');
    }

    protected function getPackageProviders($app)
    {
        return [
            \mindtwo\LaravelMissingTranslations\MissingTranslationsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('missing-translations.allowed_environments', ['testing']);
        config()->set('missing-translations.log_paused', true);
        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-translatable_table.php.stub';
        $migration->up();
        */
    }
}
