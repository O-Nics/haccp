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
            $violations[] = $file->getRealPath();
        }
    }

    if (count($violations) > 0) {
        $this->fail("Ces modèles n'étendent pas une classe de base autorisée:\n".implode("\n", $violations));
    }

    expect($violations)->toBeEmpty();
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
            $violations[] = $file->getRealPath();
        }
    }

    if (count($violations) > 0) {
        $this->fail("Ces modèles n'utilisent pas HasUuids:\n".implode("\n", $violations));
    }

    expect($violations)->toBeEmpty();
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
            $violations[] = $file->getRealPath();
        }
    }

    if (count($violations) > 0) {
        $this->fail("Fichiers d'action qui ne sont pas final readonly class:\n".implode("\n", $violations));
    }

    expect($violations)->toBeEmpty();
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
            $violations[] = $file->getRealPath();
        }
    }

    if (count($violations) > 0) {
        $this->fail("Fichiers d'action qui n'étendent pas BaseAction:\n".implode("\n", $violations));
    }

    expect($violations)->toBeEmpty();
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
            $violations[] = $file->getRealPath();
        }
    }

    if (count($violations) > 0) {
        $this->fail("Fichiers de repository qui ne sont pas final readonly class:\n".implode("\n", $violations));
    }

    expect($violations)->toBeEmpty();
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
            $violations[] = $file->getRealPath();
        }
    }

    if (count($violations) > 0) {
        $this->fail("Fichiers de repository qui n'implémentent pas RepositoryInterface:\n".implode("\n", $violations));
    }
    expect($violations)->toBeEmpty();
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
            $violations[] = $file->getRealPath();
        }
    }

    if (count($violations) > 0) {
        $this->fail("Fichiers de form request qui n'étendent pas BaseFormRequest:\n".implode("\n", $violations));
    }
    expect($violations)->toBeEmpty();
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
            $violations[] = $file->getRealPath();
        }
    }
    if (count($violations) > 0) {
        $this->fail("Fichiers sans declare(strict_types=1):\n".implode("\n", $violations));
    }

    expect($violations)->toBeEmpty();
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
                    $file->getRealPath(),
                    $lineNumber + 1
                );
            }
        }
    }

    if (count($violations) > 0) {
        $this->fail("Arrow functions (fn()) trouvées:\n".implode("\n", $violations));
    }
    expect($violations)->toBeEmpty();
});
