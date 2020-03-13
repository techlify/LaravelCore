<?php

namespace Modules\LaravelCore\Listeners;

use Illuminate\Support\Facades\Mail;
use App\Mail\clientSubscriptionMail;

class CreateDefaultClientSubscriptionsAccessControlsListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $subscription = $event->subscription;

        if ($subscription->client->email) {
            Mail::to($subscription->client->email)->queue(new clientSubscriptionMail($subscription));
        }
    }
}
