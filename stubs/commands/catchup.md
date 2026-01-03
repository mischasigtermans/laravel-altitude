---
description: Resume work after a break - get up to speed on current state
maintainer: Laravel Altitude
---

# Catchup - Resume After Break

Get up to speed on current work state.

## Usage

```
/catchup
```

## Workflow

1. **Git State**
   ```bash
   git branch --show-current
   git status
   git log --oneline -5
   git diff --stat
   ```

2. **App State**
   - Check `storage/logs/laravel.log` (last hour or 10 entries)
   - Run `php artisan test`

3. **Issue Context**
   - Parse branch for ID: `feature/LIN-123-*`, `fix/LIN-123-*`
   - If Linear MCP: fetch issue
   - If unavailable: show parsed ID

4. **Summary**
   ```
   ## Current State

   **Branch:** [name]

   ### Recent Commits
   - [summaries]

   ### Uncommitted Changes
   - [files]

   ### Issue
   - [details]

   ### App Health
   - [errors]
   - [test results]

   ### Next Steps
   1. [action]
   ```

5. **Continue** - Suggest next action or agent
