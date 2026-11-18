<?php

use Altitude\Cleanup;
use Illuminate\Support\Facades\File;

beforeEach(fn () => removeAppFiles());
afterEach(fn () => removeAppFiles());

it('knows the hash of every published 0.x file in LF and CRLF form', function () {
    expect(fixturePaths())->toHaveCount(18);

    $expected = collect(fixturePaths())->mapWithKeys(function (string $path): array {
        $contents = File::get(FIXTURES.'/'.$path);

        return [$path => [
            hash('sha256', $contents),
            hash('sha256', str_replace("\n", "\r\n", $contents)),
        ]];
    })->all();

    expect(Cleanup::HASHES)->toBe($expected);
});

it('deletes unedited 0.x files and the directories they leave empty', function () {
    placeFixtures();

    $result = (new Cleanup)->run(base_path());

    expect($result['deleted'])->toBe([...fixturePaths(), '.claude/agents/', '.claude/commands/'])
        ->and($result['edited'])->toBe([]);

    foreach (fixturePaths() as $path) {
        expect(file_exists(base_path($path)))->toBeFalse();
    }

    expect(is_dir(base_path('.claude/agents')))->toBeFalse()
        ->and(is_dir(base_path('.claude/commands')))->toBeFalse()
        ->and(is_dir(base_path('.claude')))->toBeTrue();
});

it('keeps and reports edited files', function () {
    placeFixtures();
    File::append(base_path('.claude/agents/architect.md'), "\nOur own rule.\n");
    File::append(base_path('config/altitude.php'), "\n");

    $result = (new Cleanup)->run(base_path());

    expect($result['edited'])->toBe(['.claude/agents/architect.md', 'config/altitude.php'])
        ->and(file_exists(base_path('.claude/agents/architect.md')))->toBeTrue()
        ->and(file_exists(base_path('config/altitude.php')))->toBeTrue()
        ->and(file_exists(base_path('.claude/agents/pest.md')))->toBeFalse()
        ->and(is_dir(base_path('.claude/agents')))->toBeTrue()
        ->and($result['deleted'])->not->toContain('.claude/agents/');
});

it('deletes a file checked out with CRLF line endings', function () {
    placeFixtures();
    $path = base_path('.claude/commands/ship.md');
    File::put($path, str_replace("\n", "\r\n", File::get($path)));

    $result = (new Cleanup)->run(base_path());

    expect($result['deleted'])->toContain('.claude/commands/ship.md')
        ->and(file_exists($path))->toBeFalse();
});

it('keeps a file with mixed line endings', function () {
    placeFixtures();
    $path = base_path('.claude/commands/ship.md');
    File::put($path, preg_replace("/\n/", "\r\n", File::get($path), 1));

    $result = (new Cleanup)->run(base_path());

    expect($result['edited'])->toBe(['.claude/commands/ship.md'])
        ->and(file_exists($path))->toBeTrue();
});

it('leaves unrelated files and their directories alone', function () {
    placeFixtures();
    File::put(base_path('.claude/agents/reviewer.md'), 'mine');
    File::put(base_path('.claude/commands/deploy.md'), 'mine');
    File::put(base_path('.ai/guidelines/purpose.md'), 'mine');

    $result = (new Cleanup)->run(base_path());

    expect($result['deleted'])->toBe(fixturePaths())
        ->and(File::get(base_path('.claude/agents/reviewer.md')))->toBe('mine')
        ->and(File::get(base_path('.claude/commands/deploy.md')))->toBe('mine')
        ->and(File::get(base_path('.ai/guidelines/purpose.md')))->toBe('mine');
});

it('does nothing in a project without 0.x files', function () {
    expect((new Cleanup)->run(base_path()))->toBe(['deleted' => [], 'edited' => []]);
});
