---
description: Run tests related to current changes
maintainer: Laravel Altitude
---

# Test - Run Related Tests

Identify and run tests related to current changes.

## Usage

```
/test [path] [--all] [--coverage]
```

## Arguments

- `path`: Specific test file or directory
- `--all`: Run entire suite
- `--coverage`: Include coverage report

## File-to-Test Mapping

Discovery order: `tests/Feature/` then `tests/Unit/`

| Source | Test |
|--------|------|
| `app/Models/User.php` | `tests/Feature/Models/UserTest.php` |
| `app/Livewire/Dashboard.php` | `tests/Feature/Livewire/DashboardTest.php` |
| `app/Services/Payment.php` | `tests/Unit/Services/PaymentTest.php` |

Non-standard: Check `phpunit.xml` for custom suites.

## Workflow

1. Get changed files: `git diff --name-only HEAD`
2. Map to test paths
3. Search Feature first, then Unit
4. Run with 120s timeout per file
5. Limit to 50 failing assertions

## Failure Format

```
FAILED: tests/Feature/UserTest.php::it_creates_user
  - Expected 201, got 422
  - Line 45
  - Fix: Check validation in UserRequest
```

## No Tests Found

- Suggest test creation
- Offer @pest agent

## Examples

```
/test
/test tests/Feature/UserTest.php
/test --all
```
