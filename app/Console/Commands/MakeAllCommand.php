<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:all {name : The name of the model} {--no-test : Skip test generation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a model with migration, factory, service, repository interface, repository concrete, resource, and tests';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $modelName = Str::studly($name);
        $skipTest = $this->option('no-test');

        $this->info("🚀 Creating all files for {$modelName}...");
        $this->newLine();

        // Create Model with Migration and Factory
        $this->info('📦 Creating Model, Migration, and Factory...');
        $this->call('make:model', [
            'name' => $modelName,
            '--migration' => true,
            '--factory' => true,
        ]);

        // Create Service
        $this->info('🔧 Creating Service...');
        $this->call('make:service', [
            'name' => $modelName,
        ]);

        // Create Repository Interface and Concrete with CRUD methods
        $this->info('📚 Creating Repository...');
        $this->call('make:repository', [
            'name' => $modelName,
            '--from-make-all' => true,
        ]);

        // Create Resource
        $this->info('📄 Creating Resource...');
        $this->call('make:resource', [
            'name' => "{$modelName}Resource",
        ]);

        // Create Controller
        $this->info('🎮 Creating Controller...');
        $this->createController($modelName);

        // Create Form Requests
        $this->info('📝 Creating Form Requests...');
        $this->createFormRequests($modelName);

        // Create DTOs
        $this->info('📦 Creating DTOs...');
        $this->createDTOs($modelName);

        // Add scopes to Model
        $this->info('🔍 Adding scopes to Model...');
        $this->addScopesToModel($modelName);

        // Create Test
        if (! $skipTest) {
            $this->info('🧪 Creating Tests...');
            $this->createTest($modelName);
        }

        // Auto-bind repository in AppServiceProvider
        $this->info('🔗 Binding Repository in AppServiceProvider...');
        $this->bindRepository($modelName);

        $this->newLine();
        $this->info("✅ All files created successfully for {$modelName}!");
        $this->newLine();
        $this->info('📋 Files created:');
        $this->line("   📦 Model: app/Models/{$modelName}.php (with scopes)");
        $this->line('   🗄️  Migration: database/migrations/*_create_'.Str::snake(Str::plural($modelName)).'_table.php');
        $this->line("   🏭 Factory: database/factories/{$modelName}Factory.php");
        $this->line("   🎮 Controller: app/Http/Controllers/Api/{$modelName}Controller.php");
        $this->line("   📝 Requests: app/Http/Requests/{$modelName}/Index{$modelName}Request.php");
        $this->line("                app/Http/Requests/{$modelName}/Store{$modelName}Request.php");
        $this->line("                app/Http/Requests/{$modelName}/Update{$modelName}Request.php");
        $this->line("   📦 DTOs: app/DataTransferObjects/{$modelName}/Index{$modelName}Data.php");
        $this->line("            app/DataTransferObjects/{$modelName}/Store{$modelName}Data.php");
        $this->line("            app/DataTransferObjects/{$modelName}/Update{$modelName}Data.php");
        $this->line("   🔧 Service: app/Services/{$modelName}Service.php");
        $this->line("   📚 Repository: app/Repositories/{$modelName}/{$modelName}Repository.php");
        $this->line("                  app/Repositories/{$modelName}/{$modelName}RepositoryInterface.php");
        $this->line("   📄 Resource: app/Http/Resources/{$modelName}Resource.php");
        if (! $skipTest) {
            $this->line("   🧪 Tests: tests/Feature/{$modelName}/ (5 files)");
        }
        $this->newLine();
        $this->info('📋 Next steps:');
        $this->line('   1. Update the migration: database/migrations/*_create_'.Str::snake(Str::plural($modelName)).'_table.php');
        $this->line("   2. Update the factory: database/factories/{$modelName}Factory.php");
        $this->line("   3. Update the form requests validation in: app/Http/Requests/{$modelName}/");
        $this->line("   4. Update the resource fields: app/Http/Resources/{$modelName}Resource.php");
        $this->line('   5. Add routes to routes/api.php:');
        $this->line("      Route::apiResource('".Str::kebab(Str::plural($modelName))."', {$modelName}Controller::class);");
        $this->line("   6. Update test data in: tests/Feature/{$modelName}/");
        $this->line('   7. Run migrations: php artisan migrate');
        $this->line("   8. Run tests: php artisan test --filter {$modelName}");

        return self::SUCCESS;
    }

    protected function createController(string $modelName): void
    {
        $controllerPath = app_path("Http/Controllers/Api/{$modelName}Controller.php");
        $stubPath = $this->getStub('controller');

        if (! file_exists($stubPath)) {
            $this->error("Controller stub not found: {$stubPath}");

            return;
        }

        $stub = file_get_contents($stubPath);

        // Replace placeholders
        $replacements = [
            '{{ model }}' => $modelName,
            '{{ modelLower }}' => Str::camel($modelName),
            '{{ modelPlural }}' => Str::camel(Str::plural($modelName)),
        ];

        $stub = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );

        $directory = dirname($controllerPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($controllerPath, $stub);
        $this->info("Controller created: {$controllerPath}");
    }

    protected function createFormRequests(string $modelName): void
    {
        $requestTypes = ['index', 'store', 'update'];
        $requestDirectory = app_path("Http/Requests/{$modelName}");

        // Create request directory if it doesn't exist
        if (! is_dir($requestDirectory)) {
            mkdir($requestDirectory, 0755, true);
        }

        // Generate replacements array
        $replacements = [
            '{{ model }}' => $modelName,
            '{{ modelTable }}' => Str::snake(Str::plural($modelName)),
        ];

        // Create each request file
        foreach ($requestTypes as $requestType) {
            $requestPath = "{$requestDirectory}/".Str::studly($requestType)."{$modelName}Request.php";
            $stubPath = $this->getStub("request-{$requestType}");

            if (! file_exists($stubPath)) {
                $this->error("Request stub not found: {$stubPath}");

                continue;
            }

            $stub = file_get_contents($stubPath);

            // Replace placeholders
            $stub = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $stub
            );

            file_put_contents($requestPath, $stub);
            $this->info("Request created: {$requestPath}");
        }
    }

    protected function createDTOs(string $modelName): void
    {
        $dtoTypes = ['index', 'store', 'update'];
        $dtoDirectory = app_path("DataTransferObjects/{$modelName}");

        // Create DTO directory if it doesn't exist
        if (! is_dir($dtoDirectory)) {
            mkdir($dtoDirectory, 0755, true);
        }

        // Generate replacements array
        $replacements = [
            '{{ model }}' => $modelName,
        ];

        // Create each DTO file
        foreach ($dtoTypes as $dtoType) {
            $dtoPath = "{$dtoDirectory}/".Str::studly($dtoType)."{$modelName}Data.php";
            $stubPath = $this->getStub("dto-{$dtoType}");

            if (! file_exists($stubPath)) {
                $this->error("DTO stub not found: {$stubPath}");

                continue;
            }

            $stub = file_get_contents($stubPath);

            // Replace placeholders
            $stub = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $stub
            );

            file_put_contents($dtoPath, $stub);
            $this->info("DTO created: {$dtoPath}");
        }
    }

    protected function addScopesToModel(string $modelName): void
    {
        $modelPath = app_path("Models/{$modelName}.php");

        if (! file_exists($modelPath)) {
            $this->error("Model not found: {$modelPath}");

            return;
        }

        $modelContent = file_get_contents($modelPath);

        // Check if scopes already exist
        if (str_contains($modelContent, 'scopeFiltered')) {
            $this->warn("Scopes already exist in {$modelName} model");

            return;
        }

        $stubPath = $this->getStub('model-scopes');

        if (! file_exists($stubPath)) {
            $this->error("Model scopes stub not found: {$stubPath}");

            return;
        }

        $scopesStub = file_get_contents($stubPath);
        $scopesStub = str_replace('{{ model }}', $modelName, $scopesStub);

        // Add scopes before the closing brace of the class
        $pattern = '/(\n}\s*)$/';

        if (preg_match($pattern, $modelContent)) {
            $modelContent = preg_replace(
                $pattern,
                $scopesStub."\n}",
                $modelContent
            );

            file_put_contents($modelPath, $modelContent);
            $this->info("Scopes added to {$modelName} model");
        } else {
            $this->warn("Could not automatically add scopes to {$modelName} model. Please add them manually.");
        }
    }

    protected function createTest(string $modelName): void
    {
        $testTypes = ['index', 'show', 'store', 'update', 'destroy'];
        $testDirectory = base_path("tests/Feature/{$modelName}");

        // Create test directory if it doesn't exist
        if (! is_dir($testDirectory)) {
            mkdir($testDirectory, 0755, true);
        }

        // Generate replacements array
        $replacements = [
            '{{ model }}' => $modelName,
            '{{ modelLower }}' => Str::camel($modelName),
            '{{ modelPlural }}' => Str::kebab(Str::plural($modelName)),
            '{{ modelTable }}' => Str::snake(Str::plural($modelName)),
        ];

        // Create each test file
        foreach ($testTypes as $testType) {
            $testPath = "{$testDirectory}/{$modelName}".Str::studly($testType).'Test.php';
            $stubPath = $this->getStub("test-{$testType}");

            if (! file_exists($stubPath)) {
                $this->error("Test stub not found: {$stubPath}");

                continue;
            }

            $stub = file_get_contents($stubPath);

            // Replace placeholders
            $stub = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $stub
            );

            file_put_contents($testPath, $stub);
            $this->info("Test created: {$testPath}");
        }
    }

    protected function bindRepository(string $modelName): void
    {
        $providerPath = app_path('Providers/AppServiceProvider.php');

        if (! file_exists($providerPath)) {
            $this->error("AppServiceProvider not found: {$providerPath}");

            return;
        }

        $content = file_get_contents($providerPath);

        // Check if binding already exists
        if (str_contains($content, "{$modelName}RepositoryInterface::class")) {
            $this->warn('Repository binding already exists in AppServiceProvider');

            return;
        }

        // Find the register method
        $binding = "\n        // {$modelName} Repository Binding\n";
        $binding .= "        \$this->app->bind(\n";
        $binding .= "            \\App\\Repositories\\{$modelName}\\{$modelName}RepositoryInterface::class,\n";
        $binding .= "            \\App\\Repositories\\{$modelName}\\{$modelName}Repository::class\n";
        $binding .= "        );\n";

        // Add binding before the closing brace of register method
        $pattern = '/(public function register\(\): void\s*\{(?:.*?))(    \})/s';

        if (preg_match($pattern, $content)) {
            $content = preg_replace(
                $pattern,
                "$1{$binding}$2",
                $content
            );

            file_put_contents($providerPath, $content);
            $this->info('Repository binding added to AppServiceProvider');
        } else {
            $this->warn('Could not automatically add binding. Please add it manually to AppServiceProvider:');
            $this->line($binding);
        }
    }

    protected function getStub(string $type): string
    {
        $customStub = resource_path("stubs/{$type}.stub");

        if (file_exists($customStub)) {
            return $customStub;
        }

        // Fallback to package stubs
        return __DIR__."/stubs/{$type}.stub";
    }
}
