<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use phpseclib3\Crypt\Common\PublicKey as PublicKeyInterface;
use phpseclib3\Crypt\PublicKeyLoader;
use Throwable;

// TODO: CRITICAL! Cover with tests. Definitely.

class SshPublicKeyRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (! is_string($value))
            return false;

        try {
            /** @var PublicKeyInterface $publicKey */
            $publicKey = PublicKeyLoader::load($value);
        } catch (Throwable $e) {
            return false;
        }

        if (is_null($publicKey))
            return false;

        return true;
    }

    public function message(): string
    {
        return __('validation.custom.ssh-public-key');
    }
}
