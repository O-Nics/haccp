<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Features\Session\Resources\DeviceService;
use Illuminate\Console\Command;

/**
 * Cleanup old expired devices and untrusted fingerprints
 * Should be scheduled to run daily via cron
 */
class CleanupDevicesCommand extends Command
{
    protected $signature = 'devices:cleanup';

    protected $description = 'Clean up old expired devices and untrusted fingerprints';

    public function __construct(
        private readonly DeviceService $deviceService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting device and security cleanup...');

        // Clean up expired devices globally (older than retention period)
        $deletedDevices = $this->deviceService->globalCleanupExpiredDevices();
        $this->info("Deleted {$deletedDevices} old expired devices");

        // Clean up old untrusted fingerprints
        $deletedFingerprints = $this->deviceService->cleanupOldUntrustedFingerprints();
        $this->info("Deleted {$deletedFingerprints} old untrusted fingerprints");

        // Clean up old security logs (older than 90 days)
        $deletedLogs = \App\Models\SecurityLog::where('created_at', '<', now()->subDays(90))->delete();
        $this->info("Deleted {$deletedLogs} old security logs");

        $this->newLine();
        $this->info('Cleanup completed successfully!');

        return Command::SUCCESS;
    }
}
