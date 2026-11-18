<?php

use Illuminate\Support\Facades\File;
use Laravel\Boost\Install\GuidelineAssist;
use Laravel\Boost\Install\GuidelineComposer;
use Laravel\Boost\Install\GuidelineConfig;
use Laravel\Boost\Install\Herd;
use Laravel\Roster\ProjectManager;

beforeEach(fn () => removeAppFiles());
afterEach(fn () => removeAppFiles());

/**
 * @param  list<string>  $packages
 */
function renderGuideline(array $packages = [], bool $mcp = true): string
{
    $config = new GuidelineConfig;
    $config->hasMcp = $mcp;

    $assist = Mockery::mock(GuidelineAssist::class, [new ProjectManager, $config])->makePartial();
    $assist->shouldReceive('hasPackage')->andReturnUsing(fn (string $package): bool => in_array($package, $packages, true));

    $composer = new class(new ProjectManager, new Herd, $assist) extends GuidelineComposer
    {
        public function __construct(ProjectManager $project, Herd $herd, private GuidelineAssist $assist)
        {
            parent::__construct($project, $herd);
        }

        public function render(string $path, string $key): string
        {
            return $this->guideline($path, true, $key)['content'];
        }

        protected function getGuidelineAssist(): GuidelineAssist
        {
            return $this->assist;
        }
    };

    return $composer->render(
        __DIR__.'/../../resources/boost/guidelines/core.blade.php',
        'mischasigtermans/laravel-altitude/core',
    );
}

it('renders the core guideline through Boost', function () {
    config(['app.url' => 'https://example.com']);

    $guideline = renderGuideline();

    expect($guideline)
        ->toStartWith('# Altitude')
        ->toContain('`.ai/guidelines/purpose.md`')
        ->toContain('`.ai/guidelines/vocabulary.md`')
        ->toContain('`.ai/guidelines/working.md`')
        ->toContain('`.ai/protected-surfaces.md`')
        ->toContain('`search-docs`')
        ->not->toContain('## Livewire')
        ->not->toContain('## Flux')
        ->not->toContain('## Herd')
        ->not->toContain('pint --blade')
        ->not->toContain('@if')
        ->not->toContain('hook');
});

it('adds the sections for installed packages', function () {
    config(['app.url' => 'http://example.test']);

    $guideline = renderGuideline([
        'livewire/livewire',
        'livewire/flux',
        'tailwindcss',
        'vite',
        'laravel/pint',
        'larastan/larastan',
    ]);

    expect($guideline)
        ->toContain('## Livewire')
        ->toContain('## Flux')
        ->toContain('an `@apply` rule')
        ->toContain('`herd share`')
        ->toContain('## Herd')
        ->toContain('`pint --blade`')
        ->toContain('`Attribute::make(get: fn ($value) => $value, set: ...)`');
});

it('leaves out Boost tools when its MCP server is off', function () {
    expect(renderGuideline(mcp: false))->not->toContain('search-docs');
});

it('lets a project override the guideline', function () {
    $override = base_path('.ai/guidelines/mischasigtermans/laravel-altitude/core.blade.php');
    File::ensureDirectoryExists(dirname($override));
    File::put($override, "# Our own Altitude\n");

    expect(renderGuideline())->toBe('# Our own Altitude');
});

it('uses no dashes or middle dots', function () {
    $source = File::get(__DIR__.'/../../resources/boost/guidelines/core.blade.php');

    expect($source)->not->toContain("\u{2014}")->not->toContain("\u{2013}")->not->toContain("\u{00B7}");
});
