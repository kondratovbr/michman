<?php declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Payment;

class StorePurchaseBrowserEventListener extends AbstractEventListener
{
    public function handle(SubscriptionCreated $event)
    {
        /*
         * Currently we're only doing a Reddit Pixel event:
         * https://redditinc.force.com/helpcenter/s/article/Reddit-Pixel-Event-Metadata
         */

        /** @var User $user */
        $user = $event->billable;

        $payload = [
            'transactionId' => $event->subscription->paddle_id,
        ];

        /** @var Payment|null $payment */
        $payment = rescue(fn() => $event->subscription->lastPayment());

        if ($payment) {
            $payload['value'] = (float) $payment->amount();
            $payload['currency'] = $payment->currency;
        }

        $user->addBrowserEvent(
            'reddit-event',
            'Purchase',
            $payload,
        );

        $user->save();
    }
}
