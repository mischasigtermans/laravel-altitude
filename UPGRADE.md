# Upgrade guide

## From 0.2 to 1.0

### Update the package

```bash
composer require --dev mischasigtermans/laravel-altitude:^1.0
php artisan altitude:install
```

Run `composer require` with the new constraint. A `^0.2` constraint never resolves to 1.0, so `composer update` alone keeps you on 0.2.

`altitude:install` removes the 0.x files you never edited, adds Altitude to `boost.json`, runs `boost:update` and installs the Claude Code plugin. It reports each step.

If your `composer.json` runs `boost:update` as a `post-update-cmd` script, the cleanup already ran during `composer require`. `altitude:install` then lists only the files you edited, or reports that it found none.

### Where everything went

| 0.2 | 1.0 |
|---|---|
| `/ship`, `/test`, `/review`, `/catchup` | `/altitude:ship`, `/altitude:test`, `/altitude:review`, `/altitude:catchup` in the plugin |
| `/debug` | `/altitude:diagnose` in the plugin |
| `/pint` | Gone. The plugin's format hook formats the PHP files an agent edits, when `.claude/altitude.json` turns it on. |
| `architect`, `security` agents | The plugin's `architect` and `security` agents |
| `livewire`, `alpine`, `flux`, `filament`, `pest`, `realtime`, `database`, `docs` agents | Gone. Boost's guidelines and skills cover them, and Filament and Echo ship their own Boost skills. |
| `.ai/guidelines/tall-stack.md` | The `mischasigtermans/laravel-altitude/core` guideline, composed by Boost |
| `config/altitude.php`, `ALTITUDE_AUTO_SYNC` | Gone. Remove the variable from your `.env` files. |
| `php artisan altitude:sync` | Gone. Use `php artisan altitude:install`. |

### Files you edited

The cleanup only deletes files whose bytes match what 0.x published. Anything you changed stays, and the cleanup lists it. For each one:

- An edited agent or command keeps working as a project-level agent or command. Rename it if you want to keep it next to the plugin's version.
- Edits to `tall-stack.md` belong in a guideline of your own in `.ai/guidelines/`, or in an override of Altitude's guideline at `.ai/guidelines/mischasigtermans/laravel-altitude/core.blade.php`.
- A published `config/altitude.php` does nothing in 1.0. Delete it.

### After a plain `boost:update`

If you skip `altitude:install`, the cleanup still runs after the next successful `boost:install` or `boost:update`, and lists what it removed and what it kept in that command's output. Boost has already written your agent files by then, so the old TALL guideline stays in them until the following `boost:update`. Running `altitude:install` avoids that, because it cleans up before Boost runs.
