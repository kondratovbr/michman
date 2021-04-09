<?php declare(strict_types=1);

namespace App\Services;

class DigitalOceanV2 extends AbstractServerProvider
{
    use UsesBearerTokens;

    /** @var string Bearer token used for authentication. */
    private string $token;

    public function __construct(string $token)
    {
        parent::__construct();
        $this->token = $token;
    }

    protected function getToken(): string
    {
        return $this->token;
    }

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
