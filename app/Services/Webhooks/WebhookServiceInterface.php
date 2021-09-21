<?php declare(strict_types=1);

namespace App\Services\Webhooks;

use Illuminate\Http\Request;

interface WebhookServiceInterface
{
    /** Check if the webhook signature is present and valid on a request. */
    public function signatureValid(Request $request, string $secret): bool;
}
