<?php

use Altitude\Tests\TestCase;
use Illuminate\Support\Facades\File;

uses(TestCase::class)->in(__DIR__);

const FIXTURES = __DIR__.'/Fixtures/0.2.0';

/**
 * @return list<string>
 */
function fixturePaths(): array
{
    return collect(File::allFiles(FIXTURES, hidden: true))
        ->map(fn (SplFileInfo $file): string => str_replace('\\', '/', $file->getRelativePathname()))
        ->sort()
        ->values()
        ->all();
}

function placeFixtures(): void
{
    foreach (fixturePaths() as $path) {
        File::ensureDirectoryExists(dirname(base_path($path)));
        File::copy(FIXTURES.'/'.$path, base_path($path));
    }
}

function removeAppFiles(): void
{
    File::deleteDirectory(base_path('.claude'));
    File::deleteDirectory(base_path('.ai'));
    File::delete([base_path('config/altitude.php'), base_path('boost.json')]);
}
