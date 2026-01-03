<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(base_path('.claude'));
    File::deleteDirectory(base_path('.ai'));
});

afterEach(function () {
    File::deleteDirectory(base_path('.claude'));
    File::deleteDirectory(base_path('.ai'));
});

it('syncs always-included agents', function () {
    $this->artisan('altitude:sync')
        ->assertSuccessful();

    expect(File::exists(base_path('.claude/agents/architect.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/agents/database.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/agents/docs.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/agents/security.md')))->toBeTrue();
});

it('syncs always-included commands', function () {
    $this->artisan('altitude:sync')
        ->assertSuccessful();

    expect(File::exists(base_path('.claude/commands/ship.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/commands/test.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/commands/debug.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/commands/review.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/commands/catchup.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/commands/pint.md')))->toBeTrue();
});

it('syncs guidelines', function () {
    $this->artisan('altitude:sync')
        ->assertSuccessful();

    expect(File::exists(base_path('.ai/guidelines/tall-stack.md')))->toBeTrue();
});

it('does not overwrite existing files without force', function () {
    File::ensureDirectoryExists(base_path('.claude/agents'));
    File::put(base_path('.claude/agents/architect.md'), 'custom content');

    $this->artisan('altitude:sync')
        ->assertSuccessful();

    expect(File::get(base_path('.claude/agents/architect.md')))->toBe('custom content');
});

it('overwrites existing files with force', function () {
    File::ensureDirectoryExists(base_path('.claude/agents'));
    File::put(base_path('.claude/agents/architect.md'), 'custom content');

    $this->artisan('altitude:sync', ['--force' => true])
        ->assertSuccessful();

    expect(File::get(base_path('.claude/agents/architect.md')))->not->toBe('custom content');
});

it('syncs package-specific agents when package is installed', function () {
    $lockPath = base_path('composer.lock');
    $originalLock = File::exists($lockPath) ? File::get($lockPath) : null;

    File::put($lockPath, json_encode([
        'packages' => [
            ['name' => 'livewire/livewire'],
        ],
    ]));

    $this->artisan('altitude:sync', ['--force' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('.claude/agents/livewire.md')))->toBeTrue();
    expect(File::exists(base_path('.claude/agents/alpine.md')))->toBeTrue();

    if ($originalLock) {
        File::put($lockPath, $originalLock);
    } else {
        File::delete($lockPath);
    }
});

it('does not sync package-specific agents when package is not installed', function () {
    $lockPath = base_path('composer.lock');
    $originalLock = File::exists($lockPath) ? File::get($lockPath) : null;

    File::put($lockPath, json_encode([
        'packages' => [],
    ]));

    $this->artisan('altitude:sync', ['--force' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('.claude/agents/livewire.md')))->toBeFalse();
    expect(File::exists(base_path('.claude/agents/flux.md')))->toBeFalse();
    expect(File::exists(base_path('.claude/agents/filament.md')))->toBeFalse();

    if ($originalLock) {
        File::put($lockPath, $originalLock);
    } else {
        File::delete($lockPath);
    }
});
