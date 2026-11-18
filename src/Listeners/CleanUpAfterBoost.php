<?php

namespace Altitude\Listeners;

use Altitude\Cleanup;
use Illuminate\Console\Events\CommandFinished;

class CleanUpAfterBoost
{
    public function __construct(private Cleanup $cleanup) {}

    public function handle(CommandFinished $event): void
    {
        if ($event->exitCode !== 0 || ! in_array($event->command, ['boost:install', 'boost:update'], true)) {
            return;
        }

        ['deleted' => $deleted, 'edited' => $edited] = $this->cleanup->run(base_path());

        foreach ($deleted as $path) {
            $event->output->writeln("Altitude: removed {$path}, unedited from 0.x.");
        }

        foreach ($edited as $path) {
            $event->output->writeln("Altitude: kept {$path}. You edited it, and 1.0 no longer uses it.");
        }
    }
}
