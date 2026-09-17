<?php

namespace DoeAnderson\HorizonCheck\Console;

use DoeAnderson\HorizonCheck\Exceptions\HorizonNotRunningException;
use Illuminate\Console\Command;

/**
 * Health check for Horizon, meant for cron and uptime monitors. Delegates
 * to Horizon's own status command and reports an exception whenever it
 * comes back non-zero, so the error tracker hears about a dead or paused
 * Horizon without anyone watching the console output.
 */
class HorizonCheckCommand extends Command
{
    private const int STATUS_PAUSED = 1;

    private const int STATUS_INACTIVE = 2;

    protected $signature = 'horizon:check';

    protected $description = 'Check whether Laravel Horizon is running';

    public function handle(): int
    {
        $status = $this->call('horizon:status');

        if ($status === self::SUCCESS) {
            return self::SUCCESS;
        }

        report(new HorizonNotRunningException($this->describeStatus($status)));

        return self::FAILURE;
    }

    private function describeStatus(int $status): string
    {
        return match ($status) {
            self::STATUS_PAUSED => 'Horizon is paused.',
            self::STATUS_INACTIVE => 'Horizon is inactive.',
            default => "Horizon status check exited with code {$status}.",
        };
    }
}
