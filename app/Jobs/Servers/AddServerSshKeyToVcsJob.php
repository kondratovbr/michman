<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;

class AddServerSshKeyToVcsJob extends AbstractJob
{
    public function __construct()
    {
        $this->setQueue('providers');

        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
