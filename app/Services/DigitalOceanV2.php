<?php declare(strict_types=1);

namespace App\Services;

class DigitalOceanV2 extends AbstractServerProvider
{
    protected function getConfigKey(): string
    {
        return 'digital_ocean_v2';
    }

    public function credentialsAreValid(): bool
    {
        $response = $this->get('/account');

        return $response->successful();
    }
}
