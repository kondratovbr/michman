<?php declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;

class StoreUserRegisteredBrowserEventListener extends AbstractEventListener
{
    public function handle(Registered $event)
    {
        /*
         * Currently we're only doing a Reddit Pixel event:
         * https://redditinc.force.com/helpcenter/s/article/Reddit-Pixel-Event-Metadata
         */

        /** @var User $user */
        $user = $event->user;

        $user->addBrowserEvent(
            'reddit-event',
            'SignUp',
            [
                'transactionId' => $user->uuid,
            ],
        );

        $user->save();
    }
}
