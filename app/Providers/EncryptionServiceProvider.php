<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Encryption\Encrypter;
use Illuminate\Encryption\MissingAppKeyException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class EncryptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('custom-encrypter', function () {
            return new Encrypter(
                $this->getKey(),
                config('app.cipher'),
            );
        });
    }

    protected function getKey(): string
    {
        $key = config('app.encryption_key');

        if (empty($key)) {
            throw new MissingAppKeyException('No database encryption key has been specified. See ENCRYPTION_KEY ENV variable and app.encryption_key config value.');
        }

        if (Str::startsWith($key, $prefix = 'base64:'))
            $key = base64_decode(Str::after($key, $prefix));

        return $key;
    }
}
