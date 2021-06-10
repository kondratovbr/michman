<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;

class UploadServerSshKeyToServerJob extends AbstractJob
{
    public function __construct()
    {
        $this->setQueue('default');

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
