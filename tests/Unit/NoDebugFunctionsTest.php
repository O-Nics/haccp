<?php

use Symfony\Component\Finder\Finder;

it('should not use debug functions in app code', function () {
    $debugFunctions = [
        'dd',
        'dump',
        'var_dump',
        'print_r',
        'var_export',
        'ray',
    ];

    $pattern = '/\b('.implode('|', $debugFunctions).')\s*\(/';

    $basePath = dirname(__DIR__, 2);

    $finder = new Finder;
    $finder->files()
        ->in([
            $basePath.'/app',
            $basePath.'/tests',
        ])
        ->name('*.php')
        ->notPath('vendor')
        ->notPath('Console/Commands/Test')
        ->notPath('Console/Commands/Debug')
        ->notName('NoDebugFunctionsTest.php');

    $violations = [];

    foreach ($finder as $file) {
        $content = $file->getContents();
        $lines = explode("\n", $content);

        foreach ($lines as $lineNumber => $line) {
            if (preg_match($pattern, $line, $matches)) {
                $violations[] = sprintf(
                    '%s:%d - uses %s()',
                    $file->getRealPath(),
                    $lineNumber + 1,
                    $matches[1]
                );
            }
        }
    }

    if (count($violations) > 0) {
        $this->fail("Debug functions found:\n".implode("\n", $violations));
    }

    expect($violations)->toBeEmpty();
});
