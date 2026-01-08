<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\PruneSoftDeletes::class,
    ];
protected $middlewareAliases = [
    // ... other middleware
    'permissions' => \App\Http\Middleware\CheckPermission::class,
];
    protected function schedule(Schedule $schedule)
    {
        // Production: run daily
        $schedule->command('app:prune-soft-deletes')->daily();

        // Test: every 10 seconds
        // $schedule->command('app:prune-soft-deletes')->everyTenSeconds();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
