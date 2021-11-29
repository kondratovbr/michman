<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Casts\EncryptedDtoCast;
use App\DataTransferObjects\AuthTokenDto;
use App\Models\AbstractModel;
use App\Services\ProviderInterface;
use App\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Make sure this thing is test covered.

/**
 * @property AuthTokenDto $token
 *
 * @property-read bool $expired
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

    /** Check if the currently stored token has expired. */
    public function getExpiredAttribute(): bool
    {
        return $this->token->expired();
    }

    /** Force-refresh the currently stored token. */
    public function refreshToken(): void
    {
        $this->getApi(true);
    }

    /** Get an abstract name of an API service class for this provider. */
    abstract protected function diTargetName(): string;

    /** Get an instance of provider interface to interact with the provider's API. */
    protected function getApi(bool $forceRefreshToken = false): ProviderInterface
    {
        // We're caching an instance of ServerProviderInterface for this model,
        // so it doesn't get made multiple times.
        if (isset($this->apiInstance) && ! $forceRefreshToken)
            return $this->apiInstance;

        $this->apiInstance = App::make($this->diTargetName(), ['token' => $this->token]);

        return $this->token->canExpire()
            ? $this->ensureFreshToken($forceRefreshToken)
            : $this->apiInstance;
    }

    /** Ensure the stored API token is still valid, refresh if needed. */
    protected function ensureFreshToken(bool $forceRefreshToken = false): ProviderInterface
    {
        return DB::transaction(function () use ($forceRefreshToken): ProviderInterface {
            /*
             * TODO: This logic is not entirely safe and right now I have no idea how to fix it.
             *       The freshLockForUpdate() doesn't update this current instance it only locks the row,
             *       the data may have been already changed.
             *       We can't really refresh and lock the model from inside of it, it has to be
             *       done by an external entity. So, the whole API creation logic will have to be changed to
             *       fix it.
             */

            $this->freshLockForUpdate();

            if ( ! $this->token->expired() && ! $forceRefreshToken)
                return $this->apiInstance;

            $this->token = $this->apiInstance->refreshToken();
            $this->save();

            // Remove the existing API object to reconstruct it with the new token.
            unset($this->apiInstance);

            return $this->getApi();
        }, 5);
    }
}
