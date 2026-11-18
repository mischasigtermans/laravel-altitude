<?php

namespace Altitude\Tests;

use Altitude\AltitudeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            AltitudeServiceProvider::class,
        ];
    }
}
