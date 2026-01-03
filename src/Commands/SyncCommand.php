<?php

namespace Altitude\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncCommand extends Command
{
    protected $signature = 'altitude:sync
                            {--force : Overwrite existing files}';

    protected $description = 'Sync TALL stack agents based on installed packages';

    public function handle(): void
    {
        $this->syncGuidelines();
        $agents = $this->syncAgents();
        $commands = $this->syncCommands();

        if (! $this->option('quiet')) {
            if (count($agents)) {
                $this->components->info('Agents synced:');
                $this->components->bulletList($agents);
            }

            if (count($commands)) {
                $this->components->info('Commands synced:');
                $this->components->bulletList($commands);
            }

            $this->newLine();
            $this->components->info('Altitude synced.');
        }
    }

    private function syncGuidelines(): void
    {
        $this->publish(
            'guidelines/tall-stack.md',
            '.ai/guidelines/tall-stack.md'
        );
    }

    private function syncAgents(): array
    {
        $installed = $this->installedPackages();
        $published = [];

        foreach (config('altitude.agents') as $package => $agents) {
            if (in_array($package, $installed)) {
                foreach ($agents as $agent) {
                    if ($this->publish("agents/{$agent}.md", ".claude/agents/{$agent}.md")) {
                        $published[] = $agent;
                    }
                }
            }
        }

        foreach (config('altitude.always') as $agent) {
            if ($this->publish("agents/{$agent}.md", ".claude/agents/{$agent}.md")) {
                $published[] = $agent;
            }
        }

        return array_unique($published);
    }

    private function syncCommands(): array
    {
        $installed = $this->installedPackages();
        $published = [];

        foreach (config('altitude.commands') as $package => $commands) {
            if (in_array($package, $installed)) {
                foreach ($commands as $command) {
                    if ($this->publish("commands/{$command}.md", ".claude/commands/{$command}.md")) {
                        $published[] = $command;
                    }
                }
            }
        }

        foreach (config('altitude.always_commands', []) as $command) {
            if ($this->publish("commands/{$command}.md", ".claude/commands/{$command}.md")) {
                $published[] = $command;
            }
        }

        return array_unique($published);
    }

    private function installedPackages(): array
    {
        $lockPath = base_path('composer.lock');

        if (! File::exists($lockPath)) {
            return [];
        }

        $lock = json_decode(File::get($lockPath), true);

        return collect($lock['packages'] ?? [])
            ->merge($lock['packages-dev'] ?? [])
            ->pluck('name')
            ->all();
    }

    private function publish(string $stub, string $destination): bool
    {
        $from = __DIR__."/../../stubs/{$stub}";
        $to = base_path($destination);

        if (! File::exists($from)) {
            return false;
        }

        if (File::exists($to) && ! $this->option('force')) {
            return false;
        }

        File::ensureDirectoryExists(dirname($to));
        File::copy($from, $to);

        return true;
    }
}
