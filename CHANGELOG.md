# Changelog

All notable changes to this project will be documented in this file.

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
