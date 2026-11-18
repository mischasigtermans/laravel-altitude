---
description: Review current changes for code quality and issues
maintainer: Laravel Altitude
---

# Review - Code Quality Check

Review changes for quality, security, and best practices.

## Usage

```
/review [path]
```

## Path Handling

- No path: All uncommitted changes
- Directory: All files in directory
- File: Single file

## Automated Checks

```bash
vendor/bin/pint --test
php artisan test
```

## Security Checks

### Credentials
- `password\s*=\s*['"]` (hardcoded)
- `api_key|secret|token.*=` (exposed)

### N+1 Detection
- Loops with relationship access without `with()`
- Missing `$with` property

### Authorization
- `authorize()` before mutations
- Gate checks on sensitive routes

## Severity

| Level | Examples |
|-------|----------|
| Critical | SQL injection, missing auth |
| High | N+1 in loop, no validation |
| Medium | Missing types, unclear naming |
| Low | Style, documentation |

## Pass/Fail

- **Fail**: Any Critical or High
- **Pass**: Medium/Low only

## Output

```
## Review Summary

**Status:** PASS | FAIL
**Issues:** [critical]/[high]/[medium]/[low]

### Critical/High
- [CRITICAL] file:42 - SQL injection

### Medium/Low
- [MEDIUM] file:8 - Missing type
```

## Examples

```
/review
/review app/Livewire/
```
