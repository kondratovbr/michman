<?php declare(strict_types=1);

namespace App\Console\Commands;

// NOTE: Output of this command is used when building a Docker image to verify the app version.

class PrintAppVersion extends AbstractCommand
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'version';

    /** @var string The console command description. */
    protected $description = 'Print a current app version configured in config/app.php';

    public function perform(): int
    {
        $this->line(version());

        return 0;
    }
}
