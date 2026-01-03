---
description: Format code with Laravel Pint
maintainer: Laravel Altitude
---

# Pint - Format Code

Run Laravel Pint to format code.

## Usage

```
/pint [path] [--test]
```

## Prerequisites

Check first:
```bash
test -f vendor/bin/pint || echo "Not installed"
```

If not installed: `composer require laravel/pint --dev`

## Arguments

- `path`: File or directory to format
- `--test`: Check without changes

## Logic

- No path: Format dirty files (`--dirty`)
- Path: Format that path
- `--test`: Append to either command

## Commands

```bash
vendor/bin/pint --dirty              # Dirty files
vendor/bin/pint path/to/file.php     # Specific path
vendor/bin/pint --dirty --test       # Check dirty
vendor/bin/pint path --test          # Check path
```

## Exit Codes

- 0: Pass
- 1: Files fixed (normal) or violations (test)
- 2: Config error

## Output

```
Formatted 3 file(s): file1.php, file2.php, file3.php
```

Test mode:
```
Found issues in 2 file(s): file1.php, file2.php
```

## Examples

```
/pint                     # Dirty files
/pint app/Models/         # Directory
/pint --test              # Check dirty
/pint src/File.php --test # Check file
```
