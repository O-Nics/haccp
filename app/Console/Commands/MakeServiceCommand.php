<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class with REST API methods';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $modelName = Str::studly(Str::replace('Service', '', $name));

        $servicePath = app_path("Services/{$modelName}Service.php");

        if (file_exists($servicePath)) {
            $this->error("Service already exists: {$servicePath}");

            return self::FAILURE;
        }

        $stubPath = $this->getStub('service');

        if (! file_exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");

            return self::FAILURE;
        }

        $stub = file_get_contents($stubPath);

        // Replace placeholders
        $replacements = [
            '{{ model }}' => $modelName,
            '{{ modelLower }}' => Str::camel($modelName),
        ];

        $stub = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );

        $directory = dirname($servicePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($servicePath, $stub);

        $this->info("Service created successfully: {$servicePath}");

        return self::SUCCESS;
    }

    protected function getStub(string $type): string
    {
        $customStub = resource_path("stubs/{$type}.stub");

        if (file_exists($customStub)) {
            return $customStub;
        }

        return __DIR__."/stubs/{$type}.stub";
    }
}
