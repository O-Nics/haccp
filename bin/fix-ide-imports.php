#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Fix IDE Helper imports & remove unnecessary qualifiers:
 * 1. Converts FQCN in PHPDoc to short names + adds missing `use` statements
 * 2. Replaces unnecessary \FQCN everywhere when a `use` import already exists
 * 3. Replaces \FQCN for classes in the same namespace (no import needed)
 *
 * Usage: php bin/fix-ide-imports.php [directories...]
 * Default directories: app, src/Domain
 */
$directories = array_slice($argv, 1) ?: ['app', 'src/Domain','tests'];
$basePath = dirname(__DIR__);
$fixed = 0;

foreach ($directories as $directory) {
    $path = $basePath.'/'.$directory;
    if (! is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        if (fixFile($file->getPathname())) {
            $fixed++;
        }
    }
}

echo "Fixed imports in {$fixed} file(s).\n";

function fixFile(string $filePath): bool
{
    $content = file_get_contents($filePath);
    if ($content === false) {
        return false;
    }

    // Skip files without any FQCN (leading backslash before a namespace)
    if (! str_contains($content, '\\')) {
        return false;
    }

    // Extract file namespace
    $fileNamespace = '';
    if (preg_match('/^namespace\s+([\w\\\\]+);/m', $content, $nsMatch)) {
        $fileNamespace = $nsMatch[1];
    }

    // Extract existing use statements (including aliases)
    preg_match_all('/^use\s+([\w\\\\]+?)(?:\s+as\s+(\w+))?;$/m', $content, $matches, PREG_SET_ORDER);
    $importMap = []; // shortName => FQCN
    foreach ($matches as $match) {
        $fqcn = $match[1];
        $alias = $match[2] ?? basename(str_replace('\\', '/', $fqcn));
        $importMap[$alias] = $fqcn;
    }

    // Find ALL FQCN in the file: namespaced (\Some\Ns\Class) and root-level (\Eloquent)
    // Must be preceded by a non-word char to avoid matching partial namespaces
    $fqcnPattern = '/(?<=[^a-zA-Z0-9_\\\\])\\\\((?:[A-Z][a-zA-Z0-9_]*\\\\)+[A-Z][a-zA-Z0-9_]*)/';
    preg_match_all($fqcnPattern, $content, $fqcnMatches);

    // Also match root-level classes like \Eloquent (no namespace separator)
    $rootPattern = '/(?<=[^a-zA-Z0-9_\\\\])\\\\([A-Z][a-zA-Z0-9_]*)(?![a-zA-Z0-9_\\\\])/';
    preg_match_all($rootPattern, $content, $rootMatches);

    $allFqcnMatches = array_unique(array_merge($fqcnMatches[1], $rootMatches[1]));

    $newImports = [];

    foreach ($allFqcnMatches as $fqcn) {
        $shortName = basename(str_replace('\\', '/', $fqcn));
        $fqcnNamespace = implode('\\', array_slice(explode('\\', $fqcn), 0, -1));

        // Already imported with same class — will be replaced, no new import needed
        if (isset($importMap[$shortName]) && $importMap[$shortName] === $fqcn) {
            continue;
        }

        // Same namespace — will be replaced, no import needed
        if ($fqcnNamespace !== '' && $fqcnNamespace === $fileNamespace) {
            $importMap[$shortName] = $fqcn;
            continue;
        }

        // Root-level class (no namespace) — needs a use import
        if ($fqcnNamespace === '') {
            $newImports[$shortName] = $fqcn;
            $importMap[$shortName] = $fqcn;
            continue;
        }

        // Conflict with a different existing import — skip (keep FQCN)
        if (isset($importMap[$shortName]) && $importMap[$shortName] !== $fqcn) {
            continue;
        }

        $newImports[$shortName] = $fqcn;
        $importMap[$shortName] = $fqcn;
    }

    // Replace \FQCN with short names everywhere in the file
    $modified = $content;

    // Build replacement map: only replace classes we have (or will have) imports for, or same namespace
    foreach ($importMap as $shortName => $fqcn) {
        $escaped = preg_quote($fqcn, '/');

        // Replace \FQCN — preceded by non-word char, NOT inside `use` or `namespace` lines
        $modified = preg_replace_callback(
            '/^(.*?)(?<=[^a-zA-Z0-9_\\\\])\\\\' . $escaped . '(?![a-zA-Z0-9_\\\\])/m',
            function (array $match) use ($shortName) {
                $prefix = $match[1];

                // Don't replace inside use statements or namespace declarations
                if (preg_match('/^\s*(use|namespace)\s/', $prefix)) {
                    return $match[0];
                }

                return $prefix . $shortName;
            },
            $modified
        );
    }

    // Add new use statements
    if (! empty($newImports)) {
        if (preg_match_all('/^use\s+[^;]+;$/m', $modified, $allUses, PREG_OFFSET_CAPTURE)) {
            $lastUse = end($allUses[0]);
            $insertPos = $lastUse[1] + strlen($lastUse[0]);

            $importLines = '';
            ksort($newImports);
            foreach ($newImports as $fqcn) {
                $importLines .= "\nuse {$fqcn};";
            }

            $modified = substr($modified, 0, $insertPos) . $importLines . substr($modified, $insertPos);
        }
    }

    if ($modified === $content) {
        return false;
    }

    file_put_contents($filePath, $modified);

    $relative = str_replace(dirname(__DIR__) . '/', '', $filePath);
    echo "  Fixed: {$relative}\n";

    return true;
}
