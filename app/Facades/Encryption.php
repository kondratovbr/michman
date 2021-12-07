<?php declare(strict_types=1);

namespace App\Facades;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Facade;

/**
 * Custom encryption facade to be able to use and encryption key different from APP_KEY
 * for security and administration reasons.
 *
 * @method static mixed decrypt(string $payload, bool $unserialize = true)
 * @method static string decryptString(string $payload)
 * @method static string encrypt(mixed $value, bool $serialize = true)
 * @method static string encryptString(string $value)
 *
 * @see Encrypter
 */
class Encryption extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'custom-encrypter';
    }
}
