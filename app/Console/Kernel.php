<?php declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /** @var array The Artisan commands provided by the application. */
    protected $commands = [
        //
    ];

    /** Define the application's command schedule. */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('websockets:clean')->daily();
        $schedule->command('queue:prune-batches --hours=48')->daily();
        $schedule->command('telescope:prune --hours=720')->daily();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    /** Register the commands for the application. */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
