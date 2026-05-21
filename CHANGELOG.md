# Changelog

All notable changes to `mindtwo/laravel-missing-translations` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Laravel 13 support in `composer.json` (Laravel 10, 11, 12, 13 are now supported).
- `pint.json` with the `laravel` preset and a `composer format` / `composer lint` script pair.
- PHPStan analysis raised to level 8 with array-shape and generic type hints throughout the package.
- GitHub Actions workflows for `pint`, `phpstan`, and the full `tests` matrix (PHP 8.2–8.4 × Laravel 10–13).
- Granular publish tags (`missing-translations-config`, `missing-translations-views`).

### Changed
- Replaced free-form `array` type hints with `array<TKey, TValue>` / `list<string>` shapes across the public API.
- Switched deprecated `Request::get()` calls to `Request::input()` in `MissingTranslationsController`.
- `MissingTranslationsController::renderShow()` now resolves the view through the `View` facade for a typed `view-string`.
- All in-package PHPDoc blocks were rewritten to match the Laravel framework's style.

### Removed
- Travis CI, Scrutinizer, and StyleCI configuration files (`.travis.yml`, `.scrutinizer.yml`, `.styleci.yml`).
- `phpcs.xml.dist`, `phpunit.xml.dist.bak`, and unused `tests/Mocks/ApplicationMock.php`.
- Auto-commit Pint workflow — code style is now verified in CI without mutating the repository.

### Fixed
- Dead `is_null()` guard in `MissingTranslationsController::getTranslationTable()` that could never match.
- `json_decode(file_get_contents(...))` calls now handle the `string|false` return of `file_get_contents()`.

## [1.1.3] - 2025-07-23

### Changed
- Bumped `composer.json` requirements: PHP `^8.2 || ^8.3 || ^8.4` and Laravel `^10 || ^11 || ^12`.
- Updated dev dependencies to Pest 3, Larastan 3, Orchestra Testbench 10, and Pint 1.14.

## [1.1.2] - 2024-06-19

### Changed
- Optimised translation collection paths in `FileRepository` and `DatabaseRepository`.
- `MissingTranslationsController` now resolves the active repository per-request and supports a hidden `repo` query parameter.

### Fixed
- Database repository now exposes helpers (`getMissingTranslationKeys`, `getMissingTranslationKeysForLocale`) consistent with the file repository.

## [1.1.1] - 2024-06-19

### Changed
- Reorganised configuration keys in `config/missing-translations.php` (default repository, sources, prefix, etc.).
- Adjusted service provider singleton wiring to match the new config layout.

## [1.1] - 2024-06-19

### Added
- File and database repositories behind the new `MissingTranslationRepository` contract.
- `m2:collect-missing-translations` Artisan command for persisting missing keys to the database.
- Authorization layer with a configurable Gate (`viewMissingTranslations` by default) and route middleware.
- Allowed-environments guard so the inspection route only registers in configured environments.
- `only_missing` and `exclude` filters in the browser UI.
- Initial GitHub Actions workflows for tests and PHPStan.
- Pest test suite covering authorization, logging, routing, and both repositories.

## [1.0] - 2024-01-05

### Added
- First tagged release of the package as `mindtwo/laravel-missing-translations`.
- Migration creating the `missing_translations` table (`hash`, `string`, `locale`, timestamps).
- Service provider, Eloquent model, controller, view, and route prefix configuration.
- `Lang::handleMissingKeysUsing()` integration to record missing translation keys on the fly.

[Unreleased]: https://github.com/mindtwo/missing-translations/compare/1.1.3...HEAD
[1.1.3]: https://github.com/mindtwo/missing-translations/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/mindtwo/missing-translations/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/mindtwo/missing-translations/compare/1.1...1.1.1
[1.1]: https://github.com/mindtwo/missing-translations/compare/1.0...1.1
[1.0]: https://github.com/mindtwo/missing-translations/releases/tag/1.0
