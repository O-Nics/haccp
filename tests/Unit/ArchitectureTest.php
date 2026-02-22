<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

pest()->group('architecture');

// ─────────────────────────────────────────────
// MODELS
// ─────────────────────────────────────────────

it('all domain models extend BaseModel, BaseAuthenticable, or BasePivot', function () {
    $finder = (new Finder)
        ->files()
        ->in(__DIR__.'/../../src/Domain')
        ->path('Models')
        ->name('*.php');

    $allowed = ['BaseModel', 'BaseAuthenticable', 'BasePivot'];
    $pattern = '/class\s+\w+\s+extends\s+('.implode('|', $allowed).')/';

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! preg_match('/^(class|abstract class)\s+/m', $content)) {
            continue; // skip interfaces, traits, enums
        }

        if (! preg_match($pattern, $content)) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces modèles n\'étendent pas une classe de base autorisée: '.implode(', ', $violations)
    );
});

it('all domain models use HasUuids trait', function () {
    $finder = (new Finder)
        ->files()
        ->in(__DIR__.'/../../src/Domain')
        ->path('Models')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! preg_match('/^class\s+/m', $content)) {
            continue;
        }

        if (! str_contains($content, 'HasUuids')) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces modèles n\'utilisent pas HasUuids: '.implode(', ', $violations)
    );
});

// ─────────────────────────────────────────────
// ACTIONS
// ─────────────────────────────────────────────

it('all actions are declared as final readonly class', function () {
    $finder = (new Finder)
        ->files()
        ->in(__DIR__.'/../../src/Domain')
        ->path('Actions')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! preg_match('/^final\s+readonly\s+class\s+/m', $content)) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces actions ne sont pas "final readonly class": '.implode(', ', $violations)
    );
});

it('all actions extend BaseAction', function () {
    $finder = (new Finder)
        ->files()
        ->in(__DIR__.'/../../src/Domain')
        ->path('Actions')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! str_contains($content, 'extends BaseAction')) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces actions n\'étendent pas BaseAction: '.implode(', ', $violations)
    );
});

// ─────────────────────────────────────────────
// REPOSITORIES
// ─────────────────────────────────────────────

it('all repositories are declared as final readonly class', function () {
    $finder = (new Finder)
        ->files()
        ->in(__DIR__.'/../../src/Infrastructure')
        ->path('Repositories')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! preg_match('/^final\s+readonly\s+class\s+/m', $content)) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces repositories ne sont pas "final readonly class": '.implode(', ', $violations)
    );
});

it('all repositories implement a RepositoryInterface', function () {
    $finder = (new Finder)
        ->files()
        ->in(__DIR__.'/../../src/Infrastructure')
        ->path('Repositories')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! preg_match('/implements\s+\w*RepositoryInterface/', $content)) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces repositories n\'implémentent pas une RepositoryInterface: '.implode(', ', $violations)
    );
});

// ─────────────────────────────────────────────
// FORM REQUESTS
// ─────────────────────────────────────────────

it('all form requests extend BaseFormRequest', function () {
    $finder = (new Finder)
        ->files()
        ->in(__DIR__.'/../../app/Modules')
        ->path('Requests')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! str_contains($content, 'extends BaseFormRequest')) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces requests n\'étendent pas BaseFormRequest: '.implode(', ', $violations)
    );
});

// ─────────────────────────────────────────────
// CODE STYLE
// ─────────────────────────────────────────────

it('all PHP files in app and src declare strict_types', function () {
    $finder = (new Finder)
        ->files()
        ->in([
            __DIR__.'/../../app',
            __DIR__.'/../../src',
        ])
        ->notPath('Database/Migrations')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();

        if (! str_contains($content, 'declare(strict_types=1)')) {
            $violations[] = $file->getRelativePathname();
        }
    }

    expect($violations)->toBeEmpty(
        'Ces fichiers n\'ont pas declare(strict_types=1): '.implode(', ', $violations)
    );
});

it('no arrow functions are used in app or src code', function () {
    $finder = (new Finder)
        ->files()
        ->in([
            __DIR__.'/../../app',
            __DIR__.'/../../src',
        ])
        ->notPath('Database/Migrations')
        ->name('*.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();
        $lines = explode("\n", $content);

        foreach ($lines as $lineNumber => $line) {
            // skip commented lines
            if (preg_match('/^\s*\/\//', $line)) {
                continue;
            }

            if (preg_match('/\bfn\s*\(/', $line)) {
                $violations[] = sprintf(
                    '%s:%d',
                    $file->getRelativePathname(),
                    $lineNumber + 1
                );
            }
        }
    }

    expect($violations)->toBeEmpty(
        'Arrow functions (fn()) trouvées: '.implode(', ', $violations)
    );
});
