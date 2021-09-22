<?php declare(strict_types=1);

namespace App\Services\Webhooks;

use Illuminate\Http\Request;

class GitHubWebhookService implements WebhookServiceInterface
{
    private const SIGNATURE_HEADER_NAME = 'X-Hub-Signature-256';

    public function __construct()
    {
        //
    }

    public function signatureValid(Request $request, string $secret): bool
    {
        if (! $request->hasHeader(self::SIGNATURE_HEADER_NAME))
            return false;

        $signatureProvided = $request->header(self::SIGNATURE_HEADER_NAME);

        $signatureComputed = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($signatureProvided, $signatureComputed);
    }
}
