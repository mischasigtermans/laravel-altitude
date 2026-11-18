<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto Sync
    |--------------------------------------------------------------------------
    |
    | Automatically sync agents when Boost runs install or update.
    | Disable this if you want full control over when agents are published.
    |
    */

    'auto_sync' => env('ALTITUDE_AUTO_SYNC', true),

    /*
    |--------------------------------------------------------------------------
    | Package Agent Mapping
    |--------------------------------------------------------------------------
    |
    | Maps Composer packages to their corresponding agents.
    | Agents are only published when the package is installed.
    |
    */

    'agents' => [
        'livewire/livewire' => ['livewire', 'alpine'],
        'livewire/flux' => ['flux'],
        'filament/filament' => ['filament'],
        'pestphp/pest' => ['pest'],
        'laravel/reverb' => ['realtime'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Command Mapping
    |--------------------------------------------------------------------------
    |
    | Maps Composer packages to their corresponding commands.
    | Commands are only published when the package is installed.
    |
    */

    'commands' => [
        // Package-specific commands can be added here
    ],

    /*
    |--------------------------------------------------------------------------
    | Always Include Agents
    |--------------------------------------------------------------------------
    |
    | Agents that are always published regardless of packages.
    |
    */

    'always' => [
        'architect',
        'database',
        'docs',
        'security',
    ],

    /*
    |--------------------------------------------------------------------------
    | Always Include Commands
    |--------------------------------------------------------------------------
    |
    | Commands that are always published regardless of packages.
    |
    */

    'always_commands' => [
        'ship',
        'test',
        'debug',
        'review',
        'catchup',
        'pint',
    ],

];
