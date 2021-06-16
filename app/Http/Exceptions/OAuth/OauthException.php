<?php

namespace App\Http\Exceptions\OAuth;

use Exception;
use Throwable;

class OauthException extends Exception
{
    public function __construct(
        string $oauthProvider,
        string $redirectedUrl,
        $message = null,
        $code = 0,
        Throwable $previous = null,
    ) {
        $message ??= "OAuth authentication via {$oauthProvider} failed with an error. Callback URL: {$redirectedUrl}";

        parent::__construct($message, $code, $previous);
    }
}
