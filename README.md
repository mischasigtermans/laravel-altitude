# Altitude for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mischasigtermans/laravel-altitude.svg?style=flat-square)](https://packagist.org/packages/mischasigtermans/laravel-altitude)
[![Total Downloads](https://img.shields.io/packagist/dt/mischasigtermans/laravel-altitude.svg?style=flat-square)](https://packagist.org/packages/mischasigtermans/laravel-altitude)
[![Tests](https://img.shields.io/github/actions/workflow/status/mischasigtermans/laravel-altitude/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mischasigtermans/laravel-altitude/actions/workflows/tests.yml)

Opinionated AI add-ons for Laravel, built on Laravel Boost.

Altitude is a Claude Code plugin with workflow skills, review agents and hooks that work in any repository. This package is its thin Laravel layer: it adds Altitude's Laravel guideline to [Laravel Boost](https://laravel.com/ai/boost), so every agent Boost configures reads it, and it removes what Altitude 0.x copied into your app.

## Installation

```bash
composer require --dev mischasigtermans/laravel-altitude
php artisan altitude:install
```

### Requires

- PHP 8.2+
- Laravel 12.41.1+ or 13
- Laravel Boost 2.5+ (installed with the package)
- Claude Code, for the plugin

## The plugin

The plugin is the main product. It installs once per machine and works in every project, Laravel or not:

```bash
claude plugin marketplace add mischasigtermans/by-mischa
claude plugin install altitude@by-mischa
```

It lives on the `by-mischa` marketplace. Its skills run as `/altitude:<name>`. See the [plugin repository](https://github.com/mischasigtermans/altitude) for what it ships.

## What `altitude:install` does

It runs three steps and reports each one:

1. **Cleanup.** Removes the files Altitude 0.x published that you never edited. See below.
2. **Boost.** Adds `mischasigtermans/laravel-altitude` to the `packages` list in `boost.json` and runs `boost:update`, so the guideline lands in your agent files. Without a `boost.json`, it tells you to run `php artisan boost:install` and select Altitude.
3. **Plugin.** Asks whether to install the plugin for your user (recommended) or for this project. When you run it in a terminal and `claude` is on your PATH, it runs the two commands above with that `--scope`. Otherwise, as in a script or with `--no-interaction`, it prints them for you with `--scope user`.

| Option | Effect |
|---|---|
| `--scope=user\|project` | Skip the question and install the plugin in this scope. |
| `--no-plugin` | Skip the plugin step. |
| `--marketplace=` | Add the `by-mischa` marketplace from another source, such as a local checkout. Defaults to `mischasigtermans/by-mischa`. |

The command exits non-zero only when a step fails. A skipped plugin step isn't a failure.

## What the cleanup touches

Altitude 0.x copied these files into your app:

- `.claude/agents/`: `alpine`, `architect`, `database`, `docs`, `filament`, `flux`, `livewire`, `pest`, `realtime`, `security`
- `.claude/commands/`: `catchup`, `debug`, `pint`, `review`, `ship`, `test`
- `.ai/guidelines/tall-stack.md`
- `config/altitude.php`

The cleanup deletes a file only when its bytes match what 0.x published, with LF or CRLF line endings. A file you edited stays where it is, and the command lists it so you can decide. It removes `.claude/agents` and `.claude/commands` only when they end up empty. Nothing else is read or written.

The same cleanup runs silently after every successful `boost:install` and `boost:update`. Altitude never writes a file while your app boots.

## The guideline

The guideline covers how to work in a Laravel app: fixing causes, keeping changes small, test-first bug fixes and a few Laravel traps. Sections for Livewire, Flux, Tailwind, Vite, Larastan, Pint and Herd only appear when the project uses them. It points agents at the project files the plugin's `init` skill writes: `.ai/guidelines/purpose.md`, `.ai/guidelines/vocabulary.md`, `.ai/guidelines/working.md` and `.ai/protected-surfaces.md`.

To change it for one project, put your own version at:

```
.ai/guidelines/mischasigtermans/laravel-altitude/core.blade.php
```

Boost uses that file instead and keeps it across updates.

## Upgrading from 0.2

See [UPGRADE.md](UPGRADE.md).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Credits

- [Mischa Sigtermans](https://github.com/mischasigtermans)

## License

MIT. See [LICENSE](LICENSE).
