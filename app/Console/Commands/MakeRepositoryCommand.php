<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name : The name of the repository}
                            {--crud : Include basic CRUD methods}
                            {--from-make-all : Internal flag used by make:all command}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository interface and concrete implementation';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $modelName = Str::studly(Str::replace('Repository', '', $name));

        // Determine if we should include CRUD methods
        $includeCrud = $this->option('crud') || $this->option('from-make-all');

        // Create interface
        $interfaceCreated = $this->createRepositoryInterface($modelName, $includeCrud);

        // Create concrete implementation
        $concreteCreated = $this->createRepositoryConcrete($modelName, $includeCrud);

        if ($interfaceCreated && $concreteCreated) {
            $this->newLine();
            $this->info('✅ Repository created successfully!');
            $this->newLine();
            $this->info("📝 Don't forget to bind the repository in AppServiceProvider:");
            $this->line('   $this->app->bind(');
            $this->line("       \\App\\Repositories\\{$modelName}\\{$modelName}RepositoryInterface::class,");
            $this->line("       \\App\\Repositories\\{$modelName}\\{$modelName}Repository::class");
            $this->line('   );');
            $this->newLine();
            $this->info("Or run: php artisan make:all {$modelName} to auto-bind");

            return self::SUCCESS;
        }

        return self::FAILURE;
    }

    protected function createRepositoryInterface(string $modelName, bool $includeCrud = false): bool
    {
        $interfacePath = app_path("Repositories/{$modelName}/{$modelName}RepositoryInterface.php");

        if (file_exists($interfacePath)) {
            $this->error("Repository interface already exists: {$interfacePath}");

            return false;
        }

        // Use different stub based on includeCrud flag
        $stubType = $includeCrud ? 'repository-interface' : 'repository-interface-empty';
        $stubPath = $this->getStub($stubType);

        // If empty stub doesn't exist, generate the content directly
        if (! file_exists($stubPath)) {
            $stub = $this->generateEmptyInterface($modelName);
        } else {
            $stub = file_get_contents($stubPath);
        }

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

        $directory = dirname($interfacePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($interfacePath, $stub);

        $this->info("Repository interface created: {$interfacePath}");

        return true;
    }

    protected function createRepositoryConcrete(string $modelName, bool $includeCrud = false): bool
    {
        $repositoryPath = app_path("Repositories/{$modelName}/{$modelName}Repository.php");

        if (file_exists($repositoryPath)) {
            $this->error("Repository already exists: {$repositoryPath}");

            return false;
        }

        // Use different stub based on includeCrud flag
        $stubType = $includeCrud ? 'repository' : 'repository-empty';
        $stubPath = $this->getStub($stubType);

        // If empty stub doesn't exist, generate the content directly
        if (! file_exists($stubPath)) {
            $stub = $this->generateEmptyRepository($modelName);
        } else {
            $stub = file_get_contents($stubPath);
        }

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

        $directory = dirname($repositoryPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($repositoryPath, $stub);

        $this->info("Repository created: {$repositoryPath}");

        return true;
    }

    protected function getStub(string $type): string
    {
        $customStub = resource_path("stubs/{$type}.stub");

        if (file_exists($customStub)) {
            return $customStub;
        }

        return __DIR__."/stubs/{$type}.stub";
    }

    /**
     * Generate empty interface content
     */
    protected function generateEmptyInterface(string $modelName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Repositories\\{$modelName};

interface {$modelName}RepositoryInterface
{
    //
}

PHP;
    }

    /**
     * Generate empty repository content
     */
    protected function generateEmptyRepository(string $modelName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Repositories\\{$modelName};

use App\Models\\{$modelName};

final readonly class {$modelName}Repository implements {$modelName}RepositoryInterface
{
    //
}

PHP;
    }
}
