<?php

namespace Altitude;

use Altitude\Commands\InstallCommand;
use Altitude\Listeners\CleanUpAfterBoost;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AltitudeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);

            Event::listen(CommandFinished::class, CleanUpAfterBoost::class);
        }
    }
}
