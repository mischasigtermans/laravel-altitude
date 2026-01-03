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

            if (config('altitude.auto_sync')) {
                $this->registerBoostHook();
                $this->syncOnFirstInstall();
            }
        }
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
