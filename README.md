# Laravel Missing Translations

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Tests][ico-tests]][link-tests]
[![PHPStan][ico-phpstan]][link-phpstan]
[![Total Downloads][ico-downloads]][link-packagist]

`mindtwo/laravel-missing-translations` lists every translation key your
application has used together with the value for every configured locale.
Missing keys are highlighted, optionally persisted to the database via
`Lang::handleMissingKeysUsing()`, and discoverable through a single
inspection route.

The package ships two interchangeable repositories:

- A **file repository** that diffs every translation file in the configured
  main locale against the comparison locales.
- A **database repository** backed by a `missing_translations` table,
  populated automatically as the application runs.

## Requirements

- PHP 8.2, 8.3, or 8.4
- Laravel 10, 11, 12, or 13

## Installation

```bash
composer require mindtwo/laravel-missing-translations
```

Publish the configuration file (and views, if you intend to customise them):

```bash
php artisan vendor:publish --tag=missing-translations-config
php artisan vendor:publish --tag=missing-translations-views
```

Run the package migration to enable the database repository:

```bash
php artisan migrate
```

## Configuration

`config/missing-translations.php` exposes the following options:

| Key | Description |
| --- | --- |
| `allowed_environments` | Environments where the inspection route is registered. |
| `main_locale` | Locale used as the source of truth when collecting diffs. |
| `locales` | Comparison locales. |
| `log_missing_keys` | Persist keys reported by `Lang::handleMissingKeysUsing()`. |
| `authorization.gate` | `false` to disable, `true` for the default `viewMissingTranslations` gate, or a custom gate name. |
| `authorization.middleware` | Middleware applied to the inspection route. |
| `route.prefix` | URL prefix for the inspection route. Defaults to `missing-translations`. |
| `repositories.default` | Default repository (`file` or `database`). |
| `repositories.sources` | Map of repository name → implementation class. |

## Usage

Visit `/<prefix>` (defaults to `/missing-translations`) in an allowed
environment to render the translation table. Query parameters:

- `?only_missing=1` — show only keys that are missing in at least one locale.
- `?exclude[]=de&exclude[]=fr` — hide one or more locales.
- `?repo=database` — switch to the database repository for the current request.

### Collecting missing keys manually

```bash
php artisan m2:collect-missing-translations --locales=de --locales=fr
php artisan m2:collect-missing-translations --dry-run
```

### Resolving a repository in code

```php
use mindtwo\LaravelMissingTranslations\Services\MissingTranslations;

$missing = app(MissingTranslations::class)
    ->repository('file')
    ->getMissingTranslationsForLocale('de');
```

## Testing

```bash
composer test
composer analyse
composer lint
composer format
```

## Changelog

See [CHANGELOG](CHANGELOG.md) for a list of recent changes.

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover a security issue, please email `info@mindtwo.de` instead of
opening a public issue.

## Credits

- [mindtwo GmbH][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).

[ico-version]: https://img.shields.io/packagist/v/mindtwo/laravel-missing-translations.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mindtwo/laravel-missing-translations.svg?style=flat-square
[ico-tests]: https://img.shields.io/github/actions/workflow/status/mindtwo/missing-translations/run-tests.yml?branch=master&label=tests&style=flat-square
[ico-phpstan]: https://img.shields.io/github/actions/workflow/status/mindtwo/missing-translations/phpstan.yml?branch=master&label=phpstan&style=flat-square

[link-packagist]: https://packagist.org/packages/mindtwo/laravel-missing-translations
[link-tests]: https://github.com/mindtwo/missing-translations/actions/workflows/run-tests.yml
[link-phpstan]: https://github.com/mindtwo/missing-translations/actions/workflows/phpstan.yml
[link-author]: https://github.com/mindtwo
[link-contributors]: ../../contributors
