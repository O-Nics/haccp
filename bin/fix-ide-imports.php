#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Fix IDE Helper imports: converts FQCN in PHPDoc @method/@property annotations
 * to short class names and adds missing `use` statements.
 *
 * Usage: php bin/fix-ide-imports.php [directories...]
 * Default directories: app, src/Domain
 */

$directories = array_slice($argv, 1) ?: ['app', 'src/Domain'];
$basePath = dirname(__DIR__);
$fixed = 0;

foreach ($directories as $directory) {
    $path = $basePath . '/' . $directory;
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

    // Only process files with PHPDoc blocks containing FQCN
    if (! preg_match('/\* @(?:method|property).*\\\\/', $content)) {
        return false;
    }

    // Extract existing use statements
    preg_match_all('/^use\s+([\w\\\\]+?)(?:\s+as\s+\w+)?;$/m', $content, $matches);
    $existingImports = $matches[1] ?? [];
    $importMap = [];
    foreach ($existingImports as $import) {
        $shortName = basename(str_replace('\\', '/', $import));
        $importMap[$shortName] = $import;
    }

    // Find all FQCN in @method and @property annotations
    $fqcnPattern = '/(?<=[\s|<(,])\\\\((?:[A-Z][a-zA-Z0-9]*\\\\)+[A-Z][a-zA-Z0-9]*)(?=[|<>\s(,])/';
    preg_match_all($fqcnPattern, $content, $fqcnMatches);

    $newImports = [];

    foreach (array_unique($fqcnMatches[1]) as $fqcn) {
        $shortName = basename(str_replace('\\', '/', $fqcn));

        // Skip if already imported (same class)
        if (isset($importMap[$shortName]) && $importMap[$shortName] === $fqcn) {
            continue;
        }

        // Skip if short name conflicts with a different existing import
        if (isset($importMap[$shortName]) && $importMap[$shortName] !== $fqcn) {
            continue;
        }

        $newImports[$shortName] = $fqcn;
        $importMap[$shortName] = $fqcn;
    }

    // Replace FQCN with short names in PHPDoc blocks (only for classes we can import)
    $modified = $content;
    foreach ($importMap as $shortName => $fqcn) {
        $escaped = preg_quote($fqcn, '/');
        // Replace \FQCN with short name in PHPDoc
        $modified = preg_replace(
            '/(?<=[\s|<(,])\\\\' . $escaped . '(?=[|<>\s(,])/',
            $shortName,
            $modified
        );
    }

    // Add new use statements
    if (! empty($newImports)) {
        // Find the last use statement to insert after
        if (preg_match('/^use\s+[^;]+;$/m', $modified, $lastUseMatch, PREG_OFFSET_CAPTURE)) {
            // Find the position after the last use statement
            preg_match_all('/^use\s+[^;]+;$/m', $modified, $allUses, PREG_OFFSET_CAPTURE);
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