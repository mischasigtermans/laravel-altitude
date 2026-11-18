# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - Unreleased

Altitude moves into a Claude Code plugin that works in any repository. This package becomes its thin Laravel layer: a Boost guideline, a cleanup of the 0.x files and an install command. It no longer assumes the TALL stack.

**Breaking**
- Removed every published agent and command. `architect` and `security` move to the plugin, as do `/ship`, `/test`, `/review` and `/catchup` (now `/altitude:<name>`). `/debug` becomes the plugin's `diagnose` skill. `/pint` is gone.
- Removed the `livewire`, `alpine`, `flux`, `filament`, `pest`, `realtime`, `database` and `docs` agents. Boost's own guidelines and skills, and the Boost skills Filament and Echo ship, cover them.
- Removed `.ai/guidelines/tall-stack.md`. The guideline now ships through Boost as `mischasigtermans/laravel-altitude/core`.
- Removed `php artisan altitude:sync`, `config/altitude.php` and the `ALTITUDE_AUTO_SYNC` setting.
- Requires Laravel Boost 2.5 or newer.

**Added**
- `php artisan altitude:install`: removes 0.x files, adds the package to `boost.json`, runs `boost:update` and installs the Claude Code plugin. Options: `--scope`, `--no-plugin`, `--marketplace`.
- A cleanup that deletes 0.x files byte-identical to what 0.x published (LF or CRLF) and keeps edited ones. It also runs silently after every successful `boost:install` and `boost:update`.
- A stack-neutral Laravel guideline in `resources/boost/guidelines/core.blade.php`, with sections that switch on per installed package.
- Laravel 13 in the test matrix.

**Changed**
- Altitude never writes files while the app boots. 0.x synced on the first console command.

**Migration**
See [UPGRADE.md](UPGRADE.md).

## [0.2.0] - 2026-03-23

### Added
- Laravel 13 support
- Laravel Boost 2.x support

## [0.1.1] - 2025-01-03

### Added
- Auto-sync on first install when `.claude/agents` doesn't exist

## [0.1.0] - 2025-01-03

### Added
- Initial release
- Dynamic agent publishing based on installed packages
- Core agents: architect, database, docs, security
- Package-specific agents: livewire, alpine, flux, filament, pest, realtime
- Workflow commands: ship, test, debug, review, catchup, pint
- TALL stack guidelines for `.ai/guidelines/`
- Auto-sync on `boost:install` and `boost:update` events
- Manual sync via `php artisan altitude:sync`
- Configurable package-to-agent mapping
- Optional Telescope MCP integration for enhanced debugging
- Laravel 11 and 12 support
