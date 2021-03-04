<?php declare(strict_types=1);

namespace App\Console\Commands;

class PrintAppVersion extends AbstractCommand
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'version';

    /** @var string The console command description. */
    protected $description = 'Print a current app version configured in config/app.php';

    /**
     * Perform the console command.
     */
    public function perform(): int
    {
        $this->line(version());

        return 0;
    }
}
