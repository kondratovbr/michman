<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class MailerLite
{
    private const BASE_URL = 'https://connect.mailerlite.com';

    public function __construct(
        private readonly string $token,
    ) {}

    public function upsertSubscriber(string $email): void
    {
        $this->request()
            ->post('/api/subscribers', [
                'email' => $email,
            ])
            ->throw();
    }

    private function request(): PendingRequest
    {
        return Http::withToken($this->token)
            ->baseUrl(static::BASE_URL)
            ->asJson()
            ->acceptJson();
    }
}
