---
description: Commit changes, push, and create PR
maintainer: Laravel Altitude
---

# Ship - Commit, Push, and Create PR

Commit changes, push to remote, create pull request.

## Usage

```
/ship [message]
```

## Arguments

- `message`: Optional. If omitted, generate from diff (1-2 sentences, imperative mood).

## Workflow

1. **Safety**
   - Get branch: `git branch --show-current`
   - **ABORT if on main/master** — prompt for feature branch
   - Run `git status`

2. **Review**
   - Run `git diff --stat`
   - **ABORT if sensitive files**: `.env`, `.env.*`, `credentials.json`, `*.pem`, `*.key`

3. **Quality**
   - Run `vendor/bin/pint --dirty`
   - Run `php artisan test`
   - **On failure**: ABORT. Override requires explicit "ship anyway"

4. **Commit**
   - `git add .`
   - Create commit with message

5. **Push**
   - `git push -u origin HEAD`

6. **PR**
   - `gh pr create` with:
     ```
     ## Summary
     - <changes>

     ## Test plan
     - [ ] <steps>
     ```
   - Return PR URL

## Examples

```
/ship
/ship "Add user profile page"
```
