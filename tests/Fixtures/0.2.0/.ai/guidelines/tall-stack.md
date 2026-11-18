---
description: TALL stack conventions and patterns
maintainer: Laravel Altitude
---

# TALL Stack Guidelines

This project uses the TALL stack: **T**ailwind CSS, **A**lpine.js, **L**aravel, and **L**ivewire.

## Stack Overview

| Layer | Technology | Purpose |
|-------|------------|---------|
| **Styling** | Tailwind CSS | Utility-first CSS framework |
| **Interactivity** | Alpine.js | Lightweight JavaScript for UI state |
| **Backend** | Laravel | PHP framework for application logic |
| **Reactivity** | Livewire | Server-driven reactive components |

## Key Principles

### Server-First Reactivity
Livewire handles most interactivity server-side. Use Alpine.js only for:
- Instant UI feedback (dropdowns, modals, tabs)
- State that doesn't need persistence
- Micro-interactions and animations

### Component Architecture
- Livewire components live in `app/Livewire/`
- Views in `resources/views/livewire/`
- Reusable Blade components in `resources/views/components/`

### Styling Approach
- Use Tailwind utility classes directly in markup
- Extract repeated patterns to Blade components
- Check Flux UI before building custom components

## Available Agents

### Always Available

| Agent | Use For |
|-------|---------|
| `architect` | Multi-file features, architecture decisions |
| `database` | Schema design, migrations, Eloquent models |
| `docs` | Documentation lookup and verification |
| `security` | Security audits and vulnerability checks |

### If Package Installed

| Agent | Package | Use For |
|-------|---------|---------|
| `livewire` | livewire/livewire | Reactive components, forms |
| `alpine` | livewire/livewire | Client-side interactivity |
| `flux` | livewire/flux | Flux UI components |
| `filament` | filament/filament | Admin panels, resources |
| `pest` | pestphp/pest | Testing |
| `realtime` | laravel/reverb | WebSockets, broadcasting |

## Workflow Commands

| Command | Purpose |
|---------|---------|
| `/ship` | Commit, push, and create PR |
| `/test` | Run tests related to changes |
| `/debug` | Debug using logs and errors |
| `/review` | Review code quality |
| `/catchup` | Resume work after a break |
| `/pint` | Format code with Laravel Pint |

## MCP Tools

### Documentation
Use `mcp__laravel-boost__search-docs` for version-specific documentation on all TALL stack technologies.

### Debugging (Optional)
If `lucianotonet/laravel-telescope-mcp` is installed:

| Tool | Purpose |
|------|---------|
| `mcp__laravel-telescope__requests` | HTTP requests with exceptions |
| `mcp__laravel-telescope__exceptions` | Stack traces |
| `mcp__laravel-telescope__queries` | Database queries |
| `mcp__laravel-telescope__jobs` | Queue jobs |
| `mcp__laravel-telescope__logs` | Application logs |
