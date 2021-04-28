<?php declare(strict_types=1);

namespace App\Actions\Providers;

use App\DataTransferObjects\ProviderData;
use App\Jobs\Providers\AddWorkerSshKeyToProviderJob;
use App\Models\Provider;
use Illuminate\Foundation\Bus\DispatchesJobs;

class StoreProviderAction
{
    public function execute(ProviderData $data): Provider
    {
        /** @var Provider $provider */
        $provider = $data->owner->providers()->create($data->toArray());

        return $provider;
    }
}
