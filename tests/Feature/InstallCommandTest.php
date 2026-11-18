<?php

use Altitude\Commands\InstallCommand;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\ExecutableFinder;

beforeEach(function () {
    removeAppFiles();
    Process::preventStrayProcesses();
    Process::fake();
    claudeOnPath(false);
    terminal(true);
});

afterEach(fn () => removeAppFiles());

function terminal(bool $attached): void
{
    app()->bind(InstallCommand::class, fn () => new class($attached) extends InstallCommand
    {
        public function __construct(private bool $attached)
        {
            parent::__construct();
        }

        protected function hasTerminal(): bool
        {
            return $this->attached;
        }
    });
}

function claudeOnPath(bool $present): void
{
    app()->instance(ExecutableFinder::class, new class($present) extends ExecutableFinder
    {
        public function __construct(private bool $present) {}

        public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
        {
            return $this->present ? '/usr/local/bin/'.$name : $default;
        }
    });
}

function fakeBoostUpdate(int $exitCode = 0): object
{
    $calls = new class
    {
        public int $count = 0;
    };

    Artisan::command('boost:update', function () use ($calls, $exitCode) {
        $calls->count++;

        return $exitCode;
    });

    return $calls;
}

/**
 * @param  list<string>  $command
 */
function assertRan(array $command): void
{
    Process::assertRan(fn (PendingProcess $process): bool => $process->command === $command && $process->path === base_path());
}

it('removes unedited 0.x files and reports edited ones', function () {
    placeFixtures();
    File::append(base_path('.claude/agents/architect.md'), "\nOur own rule.\n");

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->expectsOutputToContain('.claude/agents/pest.md')
        ->expectsOutputToContain('kept these Altitude 0.x files because you edited them')
        ->expectsOutputToContain('.claude/agents/architect.md')
        ->assertSuccessful();

    expect(file_exists(base_path('.claude/agents/pest.md')))->toBeFalse()
        ->and(file_exists(base_path('.claude/agents/architect.md')))->toBeTrue();
});

it('adds the package to boost.json and runs boost:update', function () {
    $boost = fakeBoostUpdate();
    File::put(base_path('boost.json'), <<<'JSON'
{
    "agents": [
        "claude_code"
    ],
    "guidelines": true,
    "packages": [
        "vendor/other"
    ]
}

JSON);

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->expectsOutputToContain('added mischasigtermans/laravel-altitude to the packages in boost.json')
        ->assertSuccessful();

    expect($boost->count)->toBe(1)
        ->and(File::get(base_path('boost.json')))->toBe(<<<'JSON'
{
    "agents": [
        "claude_code"
    ],
    "guidelines": true,
    "packages": [
        "vendor/other",
        "mischasigtermans/laravel-altitude"
    ]
}

JSON);
});

it('creates the packages list when boost.json has none', function () {
    fakeBoostUpdate();
    File::put(base_path('boost.json'), "{\n    \"guidelines\": true\n}\n");

    $this->artisan('altitude:install', ['--no-plugin' => true])->assertSuccessful();

    expect(json_decode(File::get(base_path('boost.json')), true))
        ->toBe(['guidelines' => true, 'packages' => ['mischasigtermans/laravel-altitude']]);
});

it('leaves boost.json alone when the package is listed and still runs boost:update', function () {
    $boost = fakeBoostUpdate();
    $json = "{\n    \"packages\": [\n        \"mischasigtermans/laravel-altitude\"\n    ]\n}\n";
    File::put(base_path('boost.json'), $json);

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->doesntExpectOutputToContain('added mischasigtermans/laravel-altitude')
        ->assertSuccessful();

    expect(File::get(base_path('boost.json')))->toBe($json)
        ->and($boost->count)->toBe(1);
});

it('tells the user to run boost:install when boost.json is missing', function () {
    $boost = fakeBoostUpdate();

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->expectsOutputToContain('php artisan boost:install')
        ->assertSuccessful();

    expect($boost->count)->toBe(0)
        ->and(file_exists(base_path('boost.json')))->toBeFalse();
});

it('fails when boost:update fails', function () {
    fakeBoostUpdate(1);
    File::put(base_path('boost.json'), "{}\n");

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->expectsOutputToContain('boost:update failed')
        ->assertFailed();
});

it('fails when boost:update is not available', function () {
    File::put(base_path('boost.json'), "{}\n");

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->expectsOutputToContain('boost:update command is not available')
        ->assertFailed();
});

it('fails on an invalid boost.json', function () {
    File::put(base_path('boost.json'), '{ nope');

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->expectsOutputToContain('boost.json is not valid JSON')
        ->assertFailed();
});

it('prints the plugin commands when claude is not on the PATH', function () {
    $this->artisan('altitude:install', ['--scope' => 'user'])
        ->expectsOutputToContain('claude plugin marketplace add mischasigtermans/by-mischa --scope user')
        ->expectsOutputToContain('claude plugin install altitude@by-mischa --scope user')
        ->assertSuccessful();

    Process::assertNothingRan();
});

it('prints the plugin commands when the run is not interactive', function () {
    claudeOnPath(true);

    $this->artisan('altitude:install', ['--no-interaction' => true])
        ->expectsOutputToContain('claude plugin install altitude@by-mischa --scope user')
        ->assertSuccessful();

    Process::assertNothingRan();
});

it('prints the plugin commands without asking when no terminal is attached', function () {
    claudeOnPath(true);
    terminal(false);

    $this->artisan('altitude:install')
        ->expectsOutputToContain('claude plugin install altitude@by-mischa --scope user')
        ->assertSuccessful();

    Process::assertNothingRan();
});

it('installs the plugin through claude when it is on the PATH', function () {
    claudeOnPath(true);

    $this->artisan('altitude:install', ['--scope' => 'project', '--marketplace' => '/path/to/altitude'])
        ->expectsOutputToContain('running claude plugin marketplace add /path/to/altitude --scope project')
        ->expectsOutputToContain('running claude plugin install altitude@by-mischa --scope project')
        ->assertSuccessful();

    assertRan(['claude', 'plugin', 'marketplace', 'add', '/path/to/altitude', '--scope', 'project']);
    assertRan(['claude', 'plugin', 'install', 'altitude@by-mischa', '--scope', 'project']);
});

it('tells the user each teammate runs both commands after a project install', function () {
    claudeOnPath(true);

    $this->artisan('altitude:install', ['--scope' => 'project'])
        ->expectsOutputToContain('Each teammate runs these two commands once')
        ->expectsOutputToContain('  claude plugin marketplace add mischasigtermans/by-mischa --scope project')
        ->expectsOutputToContain('  claude plugin install altitude@by-mischa --scope project')
        ->assertSuccessful();
});

it('names the teammate step when it prints the commands for a project install', function () {
    $this->artisan('altitude:install', ['--scope' => 'project'])
        ->expectsOutputToContain('claude plugin install altitude@by-mischa --scope project')
        ->expectsOutputToContain('Each teammate runs these two commands once')
        ->assertSuccessful();

    Process::assertNothingRan();

    Artisan::call('altitude:install', ['--scope' => 'project']);

    expect(substr_count(Artisan::output(), 'claude plugin install altitude@by-mischa --scope project'))->toBe(2);
});

it('leaves out the teammate step for a user install', function () {
    claudeOnPath(true);

    $this->artisan('altitude:install', ['--scope' => 'user'])
        ->doesntExpectOutputToContain('Each teammate')
        ->assertSuccessful();
});

it('asks for the scope and recommends user', function () {
    claudeOnPath(true);

    $this->artisan('altitude:install')
        ->expectsChoice('Install the Altitude plugin for Claude Code in which scope?', 'user', [
            'user' => 'User: every project on this machine (recommended)',
            'project' => 'Project: this repository. Each teammate runs two commands once.',
        ])
        ->assertSuccessful();

    assertRan(['claude', 'plugin', 'install', 'altitude@by-mischa', '--scope', 'user']);
});

it('stops and fails when a claude command fails', function () {
    claudeOnPath(true);
    Process::fake(fn (PendingProcess $process) => in_array('marketplace', $process->command, true)
        ? Process::result(errorOutput: 'network down', exitCode: 1)
        : Process::result());

    $this->artisan('altitude:install', ['--scope' => 'user'])
        ->expectsOutputToContain('network down')
        ->assertFailed();

    Process::assertRanTimes(fn (PendingProcess $process): bool => in_array('install', $process->command, true), 0);
});

it('skips the plugin with --no-plugin', function () {
    claudeOnPath(true);

    $this->artisan('altitude:install', ['--no-plugin' => true])
        ->expectsOutputToContain('Plugin: skipped')
        ->assertSuccessful();

    Process::assertNothingRan();
});

it('rejects an unknown scope', function () {
    $this->artisan('altitude:install', ['--scope' => 'global'])
        ->expectsOutputToContain('--scope must be user or project')
        ->assertFailed();
});
