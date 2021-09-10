<?php declare(strict_types=1);

namespace App\Actions\Providers;

use App\DataTransferObjects\ProviderDto;
use App\Models\Provider;
use App\Models\User;

class StoreProviderAction
{
    public function execute(ProviderDto $data, User $user): Provider
    {
        /** @var Provider $provider */
        $provider = $user->providers()->create($data->toArray());

        return $provider;
    }
}
