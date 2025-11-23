<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMailableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:mailable {name : The name of the mailable class}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new mailable class with its Blade view';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $force = $this->option('force');

        // Convert class name to proper format
        $className = Str::studly($name);
        $viewName = Str::snake($name);

        // Paths
        $mailClassPath = app_path("Mail/{$className}.php");
        $viewPath = resource_path("views/emails/{$viewName}.blade.php");

        // Check if files exist
        if (! $force) {
            if (File::exists($mailClassPath)) {
                $this->error("Mail class already exists: {$mailClassPath}");

                return Command::FAILURE;
            }

            if (File::exists($viewPath)) {
                $this->error("View already exists: {$viewPath}");

                return Command::FAILURE;
            }
        }

        // Create Mail directory if it doesn't exist
        if (! File::isDirectory(app_path('Mail'))) {
            File::makeDirectory(app_path('Mail'), 0755, true);
        }

        // Create emails directory if it doesn't exist
        if (! File::isDirectory(resource_path('views/emails'))) {
            File::makeDirectory(resource_path('views/emails'), 0755, true);
        }

        // Generate Mail class
        $mailClassContent = $this->generateMailClass($className, $viewName);
        File::put($mailClassPath, $mailClassContent);
        $this->info("Mail class created: {$mailClassPath}");

        // Generate Blade view
        $viewContent = $this->generateView($className);
        File::put($viewPath, $viewContent);
        $this->info("View created: {$viewPath}");

        $this->newLine();
        $this->components->info("Mailable [{$className}] created successfully.");

        return Command::SUCCESS;
    }

    /**
     * Generate the Mail class content
     */
    protected function generateMailClass(string $className, string $viewName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class {$className} extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        // Add your constructor parameters here
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Subject Here',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.{$viewName}',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

PHP;
    }

    /**
     * Generate the Blade view content
     */
    protected function generateView(string $className): string
    {
        $title = Str::headline($className);

        return <<<'BLADE'
@extends('layouts.mails')

@section('title', 'Your Email Title')
@section('subtitle', 'Your email subtitle')
@section('preheader', 'Email preview text - ' . config('app.name'))
{{-- Uncomment if you need an action button --}}
{{-- @section('action_url', $url) --}}
{{-- @section('action_text', 'Click here') --}}

@section('content')
    <p>Bonjour <strong>{{ $user->full_name ?? 'User' }}</strong>,</p>

    <p>
        This is your email content. Replace this with your actual message.
    </p>

    <p>
        Cordialement,<br>
        L'équipe {{ config('app.name') }}
    </p>
@endsection

BLADE;
    }
}
