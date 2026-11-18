<?php

namespace Altitude;

use Altitude\Commands\SyncCommand;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AltitudeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/altitude.php', 'altitude');

        $this->publishes([
            __DIR__.'/../config/altitude.php' => config_path('altitude.php'),
        ], 'altitude-config');

        if ($this->app->runningInConsole()) {
            $this->commands([SyncCommand::class]);

            $this->registerUpgradeNotice();

            if (config('altitude.auto_sync')) {
                $this->registerBoostHook();
                $this->syncOnFirstInstall();
            }
        }
    }

    private function registerUpgradeNotice(): void
    {
        Event::listen(CommandFinished::class, function (CommandFinished $event) {
            if (in_array($event->command, ['boost:install', 'boost:update'])) {
                $event->output->writeln([
                    '',
                    '  Altitude 1.0 is out: a Claude Code plugin replaces these agents and commands,',
                    '  and Boost now ships the stack skills. A ^0.2 constraint never installs it. Upgrade with:',
                    '',
                    '    composer require --dev mischasigtermans/laravel-altitude:^1.0',
                    '    php artisan altitude:install',
                    '',
                ]);
            }
        });
    }

    private function registerBoostHook(): void
    {
        Event::listen(CommandFinished::class, function (CommandFinished $event) {
            if (in_array($event->command, ['boost:install', 'boost:update'])) {
                Artisan::call('altitude:sync', ['--force' => true, '--quiet' => true]);
            }
        });
    }

    private function syncOnFirstInstall(): void
    {
        if (! File::exists(base_path('.claude/agents'))) {
            Artisan::call('altitude:sync', ['--quiet' => true]);
        }
    }
}
