@php
/** @var \Laravel\Boost\Install\GuidelineAssist $assist */
@endphp
# Altitude

Altitude's opinions for building this Laravel app. The project's own files come first; where they disagree with this section, follow the project.

## Project Files
Read these when they exist. Boost puts the `.ai/guidelines` files in your context; read the last one yourself.
- `.ai/guidelines/purpose.md`: what the product is and isn't, and who it serves. Check a new feature against it before you build.
- `.ai/guidelines/vocabulary.md`: one word per concept. The word on screen is the word in code. Don't introduce synonyms.
- `.ai/guidelines/working.md`: how work happens here, including the checks a change must pass before it's done. Run those checks instead of guessing them.
- `.ai/protected-surfaces.md`: areas that need a review pass before they change, such as auth, payments, migrations and legal text. Say so before you touch one.

## Working
- Fix the cause, not the symptom. A workaround is not a fix.
- Keep the change minimal: every changed line traces to the request. No speculative abstractions, configuration or error handling for cases that can't happen.
- When you fix a bug, write the test that fails on it first.
- A passing suite is evidence, not proof. For a user-facing change, check the page or response it changes.
- Stage commits by path. Never `git add .` or `git add -A`.

## Laravel
- Reach for the framework before a package or hand-rolled code: form requests for validation, policies for authorization, queued jobs for slow work.
- Eager-load the relations you use in a loop. `Model::preventLazyLoading(! app()->isProduction())` in a service provider turns a missed one into an exception.
- Never edit a migration that has run in production. Add a new one.
@if($assist->hasMcpEnabled())
- Boost's tools beat memory: `search-docs` for version-specific APIs, `database-schema` before you write a query or migration against a table.
@endif
@if($assist->hasPackage('larastan/larastan'))
- Larastan types a property's reads from its accessor's `Attribute` generic. A set-only `Attribute::set()` types every read as `never`, and a full analyse fails where a single-file run passed. Write a mutator as `Attribute::make(get: fn ($value) => $value, set: ...)`.
@endif
@if($assist->hasPackage('laravel/pint'))
- Never run `pint --blade`. It moves whitespace around echoes and directives, so the view still compiles while its rendered text changes: '3 years, 5 months' becomes '3 years ,5 months'.
@endif
@if($assist->hasPackage('livewire/livewire'))

## Livewire
- Server first. Livewire owns state that persists or needs validation. Use Alpine only for instant UI feedback such as dropdowns, modals and tabs, and for state the server never needs.
- Keep components thin. Domain logic lives in actions or models, so it can be tested without the component.
@endif
@if($assist->hasPackage('livewire/flux'))

## Flux
- Check Flux for a component before you build one. Extend it through its props and slots before you override its markup.
@endif
@if($assist->hasPackage('tailwindcss'))

## Tailwind
- Use utility classes in the markup. Extract a repeated pattern into a Blade component, not an `@apply` rule.
@endif
@if($assist->hasPackage('vite'))

## Vite
- A `public/hot` file makes the app load assets from the dev server. When styles vanish after a dev server stopped, delete `public/hot`.
@if(str_contains((string) config('app.url'), '.test'))
- Don't start a Vite dev server while `herd share` runs. It writes the tunnel address into `public/hot`, and the shared site loses its CSS and fonts.
@endif
@endif
@if(str_contains((string) config('app.url'), '.test'))

## Herd
- Don't run `herd secure` from an agent session. Without keychain access it can leave a site's Nginx config without a certificate, and every `.test` site goes down. Ask the user to secure the site in the Herd app.
@endif
