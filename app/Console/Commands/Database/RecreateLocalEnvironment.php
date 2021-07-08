<?php declare(strict_types=1);

namespace App\Console\Commands\Database;

use App\Console\Commands\AbstractCommand;
use App\Console\Commands\Traits\ForbiddenOnProduction;

class RecreateLocalEnvironment extends AbstractCommand
{
    use ForbiddenOnProduction;

    /** @var string The name and signature of the console command. */
    protected $signature = 'local:recreate';

    /** @var string The console command description. */
    protected $description = 'Completely recreate the local environment: wipe all databases, run migrations, run seeders, etc.';

    /**
     * Perform the console command.
     */
    public function perform(): int
    {
        // TODO: Do I also need to run the event broadcasting worker somewhere here to handle whatever events were created during seeding?

        if ($this->confirm('Do you want to refresh the database?', true)) {
            $this->call('db:wipe');
            $this->call('migrate');
            $this->info('The database was refreshed.');
        }

        if ($this->confirm('Do you want to flush all user sessions?', true))
            $this->call('session:flush');

        /*
        if ($this->confirm('Do you want to wipe the media library?', true)) {
            $this->call('media-library:clear', ['--quiet' => true]);
            $this->info('Media library cleared.');
        }
        */

        if ($this->confirm('Do you want to clear everything from cache?', true))
            $this->call('cache:clear');

        if ($this->confirm('Do you want to run the database seeder?', true))
            $this->call('db:seed');

        $this->info('Local environment recreated.');

        return 0;
    }
}
