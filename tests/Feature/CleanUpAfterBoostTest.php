<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    removeAppFiles();

    // Laravel only turns Symfony's console events into CommandFinished outside unit tests.
    $kernel = app(Kernel::class);
    $kernel->rerouteSymfonyCommandEvents();
    $kernel->setArtisan(null);
});
afterEach(fn () => removeAppFiles());

function fakeBoostCommand(string $name, int $exitCode): void
{
    Artisan::command($name, fn () => $exitCode);
}

it('cleans up after a successful boost run', function (string $command) {
    fakeBoostCommand($command, 0);
    placeFixtures();

    Artisan::call($command);

    expect(file_exists(base_path('.claude/agents/architect.md')))->toBeFalse()
        ->and(file_exists(base_path('config/altitude.php')))->toBeFalse();
})->with(['boost:install', 'boost:update']);

it('reports what it removed and what it kept', function () {
    fakeBoostCommand('boost:update', 0);
    placeFixtures();
    File::append(base_path('.claude/agents/architect.md'), "\nOur own rule.\n");

    Artisan::call('boost:update');

    expect(Artisan::output())
        ->toContain('Altitude: removed .claude/agents/pest.md')
        ->toContain('Altitude: kept .claude/agents/architect.md');
});

it('does nothing after a failed boost run', function () {
    fakeBoostCommand('boost:update', 1);
    placeFixtures();

    Artisan::call('boost:update');

    expect(file_exists(base_path('.claude/agents/architect.md')))->toBeTrue();
});

it('does nothing after other commands', function () {
    placeFixtures();

    Artisan::call('list');

    expect(file_exists(base_path('.claude/agents/architect.md')))->toBeTrue();
});

it('never touches files while the app boots', function () {
    placeFixtures();

    $this->refreshApplication();

    foreach (fixturePaths() as $path) {
        expect(file_exists(base_path($path)))->toBeTrue();
    }
});

it('writes nothing while the app boots', function () {
    $this->refreshApplication();

    expect(file_exists(base_path('.claude')))->toBeFalse()
        ->and(file_exists(base_path('.ai')))->toBeFalse();
});
