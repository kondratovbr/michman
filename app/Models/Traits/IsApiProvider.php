<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Casts\EncryptedDtoCast;
use App\DataTransferObjects\AuthTokenDto;
use App\Models\AbstractModel;
use App\Services\ProviderInterface;
use App\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * @property AuthTokenDto $token
 *
 * @mixin AbstractModel
 */
trait IsApiProvider
{
    /** Interface object to interact with the API. */
    private ProviderInterface $apiInstance;

    public function initializeIsApiProvider(): void
    {
        $this->fillable = Arr::merge($this->fillable, ['token']);

        $this->casts = Arr::merge($this->casts, ['token' => EncryptedDtoCast::class . ':' . AuthTokenDto::class]);
    }

    /** Get an abstract name of an API service class for this provider. */
    abstract protected function diTargetName(): string;

    /** Get an instance of provider interface to interact with the provider's API. */
    protected function getApi(): ProviderInterface
    {
        // We're caching an instance of ServerProviderInterface for this model,
        // so it doesn't get made multiple times.
        if (isset($this->apiInstance))
            return $this->apiInstance;

        $this->apiInstance = App::make($this->diTargetName(), ['token' => $this->token]);

        return isset($this->token->expiresAt)
            ? $this->ensureFreshToken()
            : $this->apiInstance;
    }

    /** Ensure the stored API token is still valid, refresh if needed. */
    protected function ensureFreshToken(): ProviderInterface
    {
        return DB::transaction(function (): ProviderInterface {
            $model = $this->freshLockForUpdate();
            $this->refresh();

            if ($this->token->expires_at->greaterThan(now()))
                return $this->apiInstance;

            $this->token = $this->apiInstance->refreshToken();
            $this->save();

            // Remove the existing API object to reconstruct it with the new token.
            unset($this->apiInstance);

            return $this->getApi();
        }, 5);
    }
}
