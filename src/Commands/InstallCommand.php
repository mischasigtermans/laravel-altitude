<?php

namespace Altitude\Commands;

use Altitude\Cleanup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\ExecutableFinder;

class InstallCommand extends Command
{
    public const PACKAGE = 'mischasigtermans/laravel-altitude';

    protected $signature = 'altitude:install
                            {--scope= : Where to install the Claude Code plugin: user or project}
                            {--no-plugin : Skip the Claude Code plugin}
                            {--marketplace=mischasigtermans/by-mischa : The marketplace that holds the plugin}';

    protected $description = 'Remove Altitude 0.x files, add Altitude to Boost and install the Claude Code plugin';

    public function handle(Cleanup $cleanup): int
    {
        $succeeded = $this->cleanUp($cleanup);
        $succeeded = $this->updateBoost() && $succeeded;
        $succeeded = $this->installPlugin() && $succeeded;

        return $succeeded ? self::SUCCESS : self::FAILURE;
    }

    private function cleanUp(Cleanup $cleanup): bool
    {
        ['deleted' => $deleted, 'edited' => $edited] = $cleanup->run(base_path());

        if ($deleted === [] && $edited === []) {
            $this->components->info('Cleanup: no Altitude 0.x files found.');

            return true;
        }

        if ($deleted !== []) {
            $this->components->info('Cleanup: removed the Altitude 0.x files you never edited.');
            $this->components->bulletList($deleted);
        }

        if ($edited !== []) {
            $this->components->warn('Cleanup: kept these Altitude 0.x files because you edited them. Altitude 1.0 no longer uses them; delete them when you no longer need them.');
            $this->components->bulletList($edited);
        }

        return true;
    }

    private function updateBoost(): bool
    {
        $path = base_path('boost.json');

        if (! is_file($path)) {
            $this->components->warn('Boost: no boost.json found. Run `php artisan boost:install` and select '.self::PACKAGE.'.');

            return true;
        }

        $config = json_decode((string) file_get_contents($path), true);

        if (! is_array($config)) {
            $this->components->error('Boost: boost.json is not valid JSON.');

            return false;
        }

        $packages = $config['packages'] ?? [];

        if (! in_array(self::PACKAGE, $packages, true)) {
            $config['packages'] = [...$packages, self::PACKAGE];

            file_put_contents($path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

            $this->components->info('Boost: added '.self::PACKAGE.' to the packages in boost.json.');
        }

        if (! $this->getApplication()?->has('boost:update')) {
            $this->components->error('Boost: the boost:update command is not available. Boost only runs in a local environment or with APP_DEBUG=true.');

            return false;
        }

        if ($this->call('boost:update', ['--no-interaction' => true]) !== self::SUCCESS) {
            $this->components->error('Boost: boost:update failed.');

            return false;
        }

        $this->components->info('Boost: guidelines updated.');

        return true;
    }

    private function installPlugin(): bool
    {
        if ($this->option('no-plugin')) {
            $this->components->info('Plugin: skipped.');

            return true;
        }

        $scope = $this->option('scope');

        if ($scope !== null && ! in_array($scope, ['user', 'project'], true)) {
            $this->components->error('Plugin: --scope must be user or project.');

            return false;
        }

        $interactive = $this->input->isInteractive() && $this->hasTerminal();

        $scope ??= $interactive
            ? $this->choice('Install the Altitude plugin for Claude Code in which scope?', [
                'user' => 'User: every project on this machine (recommended)',
                'project' => 'Project: this repository. Each teammate runs two commands once.',
            ], 'user')
            : 'user';

        $commands = [
            ['claude', 'plugin', 'marketplace', 'add', $this->option('marketplace'), '--scope', $scope],
            ['claude', 'plugin', 'install', 'altitude@by-mischa', '--scope', $scope],
        ];

        if (! $interactive || ! $this->claudeIsInstalled()) {
            $this->components->info('Plugin: install the Altitude plugin for Claude Code with these two commands:');

            foreach ($commands as $command) {
                $this->line('  '.$this->display($command));
            }

            $this->newLine();
            $this->teammates($scope, $commands);

            return true;
        }

        foreach ($commands as $command) {
            $this->components->info('Plugin: running '.$this->display($command));

            $result = Process::path(base_path())->run($command);

            $output = trim($result->output()."\n".$result->errorOutput());

            if ($output !== '') {
                $this->line($output);
                $this->newLine();
            }

            if ($result->failed()) {
                $this->components->error('Plugin: '.$this->display($command).' failed. Fix the error above and run it again.');

                return false;
            }
        }

        $this->teammates($scope, $commands);

        return true;
    }

    /**
     * @param  list<list<string>>  $commands
     */
    private function teammates(string $scope, array $commands): void
    {
        if ($scope !== 'project') {
            return;
        }

        $this->components->info('Plugin: Each teammate runs these two commands once in this repository. Committed settings alone don\'t load the plugin:');

        foreach ($commands as $command) {
            $this->line('  '.$this->display($command));
        }

        $this->newLine();
    }

    /**
     * Symfony Console 8 treats a run without a terminal as interactive, so a
     * script would answer every question with its default.
     */
    protected function hasTerminal(): bool
    {
        return stream_isatty(STDIN);
    }

    private function claudeIsInstalled(): bool
    {
        return app(ExecutableFinder::class)->find('claude') !== null;
    }

    /**
     * @param  list<string>  $command
     */
    private function display(array $command): string
    {
        return implode(' ', array_map(
            fn (string $argument): string => preg_match('/^[\w@.\/:=-]+$/', $argument) ? $argument : escapeshellarg($argument),
            $command,
        ));
    }
}
