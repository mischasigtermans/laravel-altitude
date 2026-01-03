# Altitude

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mischasigtermans/laravel-altitude.svg?style=flat-square)](https://packagist.org/packages/mischasigtermans/laravel-altitude)
[![Total Downloads](https://img.shields.io/packagist/dt/mischasigtermans/laravel-altitude.svg?style=flat-square)](https://packagist.org/packages/mischasigtermans/laravel-altitude)

Opinionated Claude Code agents and commands for TALL stack development.

> **Note:** This package is opinionated. It assumes you're building with the TALL stack (Tailwind, Alpine, Livewire, Laravel) and optionally Filament, Flux UI, and Pest. If you use a different stack, this package may not be for you.

Altitude provides specialized AI agents that focus on decision-making and workflow patterns while [Laravel Boost](https://laravel.com/ai/boost) handles version-specific documentation via MCP.

## Installation

```bash
composer require mischasigtermans/laravel-altitude --dev
```

## Quick Start

Altitude syncs automatically when Laravel Boost runs `boost:install` or `boost:update`.

**Recommended:** Add to your `composer.json` scripts for automatic updates:

```json
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan boost:update --quiet"
        ]
    }
}
```

This keeps agents and documentation in sync whenever you run `composer update`.

**Manual sync:** For more control, run directly:

```bash
php artisan altitude:sync         # Sync new agents only
php artisan altitude:sync --force # Overwrite existing agents
```

## How Syncing Works

### On Install

When you install Altitude, agents sync automatically on the next console command if `.claude/agents` doesn't exist yet. This ensures you get agents immediately without needing to run a separate command.

### On Boost Updates

When `boost:install` or `boost:update` runs, Altitude syncs with `--force`, updating all agents to the latest versions.

### File Conflicts

Altitude agents use common names like `architect`, `database`, and `livewire`. If you have custom agents with the same names, they will be overwritten on boost updates.

**To keep custom agents**, use different names:

```
.claude/agents/
├── architect.md        # Altitude (will be updated)
├── my-architect.md     # Your custom version (safe)
└── project-rules.md    # Your custom agent (safe)
```

Alternatively, disable auto-sync and manage updates manually:

```env
ALTITUDE_AUTO_SYNC=false
```

## Why Altitude?

AI assistants need context about your stack to give good advice. But documentation changes between versions, and copy-pasting docs into prompts wastes tokens and goes stale.

Altitude solves this by:

- **Separating concerns** — Agents handle decisions and workflow, Boost handles docs
- **Package-aware publishing** — Only get agents for packages you actually use
- **Workflow commands** — Common tasks like `/ship`, `/test`, `/debug` work out of the box
- **Never stale** — Agents reference `mcp__laravel-boost__search-docs` instead of embedding version-specific code

## Agents

Altitude publishes agents based on your installed packages:

### Always Included

| Agent | Purpose |
|-------|---------|
| `architect` | Multi-file features and architecture decisions |
| `database` | Schema design, migrations, Eloquent models |
| `docs` | Documentation lookup via MCP tools |
| `security` | Security audits and vulnerability checks |

### Package-Specific

| Package | Agents |
|---------|--------|
| `livewire/livewire` | `livewire`, `alpine` |
| `livewire/flux` | `flux` |
| `filament/filament` | `filament` |
| `pestphp/pest` | `pest` |
| `laravel/reverb` | `realtime` |

### How Agents Work

Each agent defines:
1. **Responsibilities** — What it handles
2. **Decision guides** — Common choices and tradeoffs
3. **Workflow** — Steps to follow
4. **References** — Points to `mcp__laravel-boost__search-docs` for implementation details

Example usage in Claude Code:

```
@architect I need to add a booking system with events, tickets, and payments
```

```
@livewire Create a component for filtering products by category
```

```
@database Design a schema for multi-tenant organizations with team members
```

## Commands

Workflow commands are always published:

| Command | Purpose |
|---------|---------|
| `/ship` | Commit, push, and create pull request |
| `/test` | Run tests related to current changes |
| `/debug` | Debug using logs and application state |
| `/review` | Review code for quality and security |
| `/catchup` | Resume work after a break |
| `/pint` | Format code with Laravel Pint |

### Command Examples

```
/ship "Add user profile page"
```

```
/test --all
```

```
/debug "500 error on checkout"
```

## Optional: Enhanced Debugging

### Herd Pro

If you're using [Laravel Herd Pro](https://herd.laravel.com), the `/debug` command can access logs and dumps via MCP:

| Tool | Purpose |
|------|---------|
| `mcp__herd__get-logs` | Application logs |
| `mcp__herd__get-dumps` | Dump output |

### Telescope MCP

If you have [Laravel Telescope](https://laravel.com/docs/telescope) installed, add the MCP wrapper for Claude access:

```bash
composer require lucianotonet/laravel-telescope-mcp --dev
```

| Tool | Purpose |
|------|---------|
| `mcp__laravel-telescope__requests` | HTTP requests with exceptions |
| `mcp__laravel-telescope__exceptions` | Stack traces and error details |
| `mcp__laravel-telescope__queries` | Slow and failed database queries |
| `mcp__laravel-telescope__jobs` | Failed queue jobs |
| `mcp__laravel-telescope__logs` | Application logs |
| `mcp__laravel-telescope__mail` | Sent emails |

Without these tools, `/debug` falls back to reading `storage/logs/laravel.log`.

## What Gets Published

```
.ai/
└── guidelines/
    └── tall-stack.md          # Stack overview and conventions

.claude/
├── agents/
│   ├── alpine.md              # Alpine.js interactivity
│   ├── architect.md           # Architecture decisions
│   ├── database.md            # Schema and migrations
│   ├── docs.md                # Documentation lookup
│   ├── filament.md            # Filament admin panels
│   ├── flux.md                # Flux UI components
│   ├── livewire.md            # Livewire components
│   ├── pest.md                # Testing with Pest
│   ├── realtime.md            # WebSockets with Reverb
│   └── security.md            # Security auditing
└── commands/
    ├── catchup.md             # Resume after break
    ├── debug.md               # Debug with logs/Telescope
    ├── pint.md                # Code formatting
    ├── review.md              # Code review
    ├── ship.md                # Commit and PR workflow
    └── test.md                # Run related tests
```

## Configuration

Publish the config to customize:

```bash
php artisan vendor:publish --tag=altitude-config
```

```php
// config/altitude.php
return [
    // Disable auto-sync on boost:install/update
    'auto_sync' => env('ALTITUDE_AUTO_SYNC', true),

    // Map packages to agents
    'agents' => [
        'livewire/livewire' => ['livewire', 'alpine'],
        'livewire/flux' => ['flux'],
        'filament/filament' => ['filament'],
        'pestphp/pest' => ['pest'],
        'laravel/reverb' => ['realtime'],
    ],

    // Agents always published
    'always' => [
        'architect',
        'database',
        'docs',
        'security',
    ],

    // Commands always published
    'always_commands' => [
        'ship',
        'test',
        'debug',
        'review',
        'catchup',
        'pint',
    ],
];
```

## Re-syncing

When you add packages, re-run sync to get their agents:

```bash
composer require filament/filament
php artisan altitude:sync
```

Use `--force` to update existing agents with latest versions:

```bash
php artisan altitude:sync --force
```

## Use Cases

### New Feature Development

```
@architect I need to add a document management system with versioning
```

```
@livewire Create the upload component based on the architecture plan
```

### Debugging Production Issues

```
/debug "500 error on checkout page"
```

### Code Review Before PR

```
/review
/ship "Add document versioning"
```

### Resuming After a Break

```
/catchup
```

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- [Laravel Boost](https://laravel.com/ai/boost) (installed automatically)

## Credits

- [Mischa Sigtermans](https://github.com/mischasigtermans)

## License

MIT
