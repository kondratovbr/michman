<?php declare(strict_types=1);

namespace App\Actions\OAuthUsers;

use App\Models\OAuthUser;
use App\Models\User;

class CreateOAuthUserAction
{
    public function execute(string $oauthProvider, string|int $oauthId, User $user): OAuthUser
    {
        /** @var OAuthUser $oauthUser */
        $oauthUser = $user->oauthUsers()->create([
            'provider' => $oauthProvider,
            'oauth_id' => $oauthId,
        ]);

        return $oauthUser;
    }
}
