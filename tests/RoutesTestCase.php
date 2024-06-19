<?php

namespace mindtwo\LaravelMissingTranslations\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class RoutesTestCase extends Orchestra
{
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
        return [];
    }

    public function updateConfig(array $values): void
    {
        $this->artisan('route:clear');
        $this->artisan('config:clear');

        foreach ($values as $key => $value) {
            $this->app['config']->set($key, $value);
        }
        $this->app->register(\mindtwo\LaravelMissingTranslations\MissingTranslationsServiceProvider::class);
    }

    public function getEnvironmentSetUp($app)
    {
        // config()->set('missing-translations.allowed_environments', ['testing']);

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-translatable_table.php.stub';
        $migration->up();
        */
    }
}
