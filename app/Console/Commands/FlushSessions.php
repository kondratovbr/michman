<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\ProtectedOnProduction;
use App\Exceptions\NotImplementedException;
use Illuminate\Support\Facades\DB;

class FlushSessions extends AbstractCommand
{
    use ProtectedOnProduction;
    
    /** @var string The name and signature of the console command. */
    protected $signature = 'session:flush';

    /** @var string The console command description. */
    protected $description = 'Flush all user sessions';

    /**
     * Perform the console command.
     */
    public function perform(): int
    {
        $driver = config('session.driver');
        $flushMethodName = 'flush' . ucfirst($driver);
        if (method_exists($this, $flushMethodName)) {
            try {
                $this->$flushMethodName();
                $this->info('Session data cleaned.');
            } catch (\Throwable $exception) {
                $this->error($exception->getMessage());
                return 1;
            }
        } else {
            $this->error("Sorry, I don't know how to clean the sessions of the driver '{$driver}'.");
            return 1;
        }

        return 0;
    }

    /**
     * Sessions flush method for sessions driver 'file'.
     */
    protected function flushFile(): void {
        $directory = config('session.files');
        $ignoreFiles = ['.gitignore', '.', '..'];

        $files = scandir($directory);

        foreach ($files as $file) {
            if(! in_array($file,$ignoreFiles)) {
                unlink($directory . '/' . $file);
            }
        }
    }

    /**
     * Sessions flush method for sessions driver 'database'.
     */
    protected function flushDatabase(): void {
        $table = config('session.table');
        DB::table($table)->truncate();
    }

    // TODO: Implement sessions flush method for Redis.
    /**
     * Placeholder for sessions flush method for Redis.
     */
    protected function flushRedis(): void {
        throw new NotImplementedException("Sorry, I don't know how to clean the sessions of the driver 'Redis', yet.");
    }
}
