---
description: Debug current issue using logs and errors
maintainer: Laravel Altitude
---

# Debug - Investigate Errors

Investigate errors using available diagnostic tools.

## Usage

```
/debug [error context]
```

## Issue Classification

```
Error Type?
├── HTTP → Telescope requests, route logs
├── Database → Telescope queries, slow log
├── Queue → failed_jobs, Telescope jobs
└── Auth → Telescope requests, session
```

## Investigation

### 1. Gather Context

**If Herd MCP available** (Laravel Herd Pro):
```
mcp__herd__get-logs                 # Application logs
mcp__herd__get-dumps                # Dump output
```

**If Telescope MCP installed** (`lucianotonet/laravel-telescope-mcp`):
```
mcp__laravel-telescope__requests    # HTTP with exceptions
mcp__laravel-telescope__exceptions  # Stack traces
mcp__laravel-telescope__queries     # Slow/failed queries
mcp__laravel-telescope__jobs        # Failed jobs
mcp__laravel-telescope__logs        # Application logs
mcp__laravel-telescope__mail        # Email issues
```

**Fallback:**
- Read `storage/logs/laravel.log`
- Search for error messages
- Check `failed_jobs` table

### 2. Trace

- Follow stack trace to file:line
- Read surrounding code
- Check related files

### 3. Report

```
## Debug Report

**Error:** [Exception] - [message]
**Location:** [file:line]

### Root Cause
[What's failing]

### Fix
[Code change]
```

## Examples

```
/debug
/debug "500 on checkout"
/debug "jobs stuck"
```
