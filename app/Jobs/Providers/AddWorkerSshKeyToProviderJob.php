<?php declare(strict_types=1);

namespace App\Jobs\Providers;

use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AddWorkerSshKeyToProviderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Provider $provider;
    protected bool $force;

    /**
     * @param bool $force Add a new SSH key even if we already had another one added.
     */
    public function __construct(Provider $provider, bool $force = false)
    {
        $this->onQueue('providers');

        $this->provider = $provider->withoutRelations();
        $this->force = $force;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Provider $provider */
            $provider = Provider::query()
                ->lockForUpdate()
                ->where(Provider::keyName(), $this->provider->getKey())
                ->firstOrFail();

            if ($provider->sshKeyAdded && ! $this->force)
                return;

            $addedKey = $provider->api()->addSshKeySafely(
                (string) config('app.ssh_key.name'),
                trim(File::get(base_path(config('app.ssh_key.public_key_path'))))
            );

            $provider->sshKeyAdded = true;
            $provider->providerSshKeyId = $addedKey->id;
            $provider->save();
        }, 5);
    }
}
