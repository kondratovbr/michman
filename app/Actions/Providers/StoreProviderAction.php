<?php declare(strict_types=1);

namespace App\Actions\Providers;

use App\DataTransferObjects\ProviderData;
use App\Models\Provider;

class StoreProviderAction
{
    public function execute(ProviderData $data): Provider
    {
        /** @var Provider $provider */
        $provider = $data->owner->providers()->create($data->toArray());

        return $provider;
    }
}
