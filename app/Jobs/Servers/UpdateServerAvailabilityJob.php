<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use Illuminate\Bus\Queueable;
// TODO: IMPORTANT! Do I need this in the actual job? And on other server-related jobs?
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateServerAvailabilityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->onQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO: CRITICAL! CONTINUE!

        //
    }
}
