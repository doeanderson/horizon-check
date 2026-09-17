<?php

namespace DoeAnderson\HorizonCheck;

use DoeAnderson\HorizonCheck\Console\HorizonCheckCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class HorizonCheckServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/horizon-check.php', 'horizon-check');
    }

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/horizon-check.php' => config_path('horizon-check.php'),
        ], 'horizon-check-config');

        $this->commands([HorizonCheckCommand::class]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $this->scheduleCheck($schedule);
        });
    }

    private function scheduleCheck(Schedule $schedule): void
    {
        $expression = config('horizon-check.schedule');

        if (blank($expression)) {
            return;
        }

        $schedule->command('horizon:check')
            ->cron($expression)
            ->name('horizon-check');
    }
}
