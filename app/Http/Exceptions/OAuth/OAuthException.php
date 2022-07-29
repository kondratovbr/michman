<?php

namespace App\Http\Exceptions\OAuth;

use Exception;
use Throwable;

class OAuthException extends Exception
{
    public function __construct(
        string $oauthProvider,
        string $redirectedUrl,
        $message = null,
        $code = 0,
        Throwable $previous = null,
    ) {
        $message ??= "OAuth callback via $oauthProvider failed with an error. Callback URL: $redirectedUrl";

        parent::__construct($message, $code, $previous);
    }
}
