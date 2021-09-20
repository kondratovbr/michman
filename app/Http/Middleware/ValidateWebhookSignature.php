<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Exceptions\InvalidWebhookSignatureException;
use Closure;
use Illuminate\Http\Request;

class ValidateWebhookSignature
{
    // TODO: CRITICAL! Unfinished.

    public function handle(Request $request, Closure $next)
    {
        $signatureProvided = $request->header('X-Hub-Signature-256');

        // TODO: CRITICAL! Make this either per-user or per-project or something similar. Somehow.
        $secret = 'base64:onz7sa1O+VMw0lSGdTCFDS1qGVe7FmqAkFriG/VLguc=';

        // TODO: CRITICAL! Verify here that secret was provided at all.
        
        //

        $signatureComputed = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        ray($signatureProvided, $signatureComputed, $signatureComputed === $signatureProvided);

        if (! hash_equals($signatureProvided, $signatureComputed)) {
            ray('Invalid signature!');
            throw new InvalidWebhookSignatureException;
        } else {
            ray('Valid signature.');
        }

        return $next($request);
    }
}
