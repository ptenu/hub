<?php

namespace App\Observers;

use App\Extensions\Stripe;
use App\Jobs\PersistMembershipStatus;
use App\Jobs\SyncStripeCustomer;
use App\Models\Contact;
use Stripe\Exception\ApiErrorException;

class ContactObserver
{

    /**
     * Handle the Contact "updated" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function updated(Contact $contact): void
    {
        // Persist status
        if ($contact->membership != null) {
            PersistMembershipStatus::dispatch($contact->membership);
        }

        // Handle stripe stuff
        if (!is_null($contact->stripe_customer_id)) {
            SyncStripeCustomer::dispatch($contact);
        }
    }

    /**
     * Handle the Contact "force deleted" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function forceDeleted(Contact $contact): void
    {
        if (is_null($contact->stripe_customer_id)) {
            return;
        }

        $stripe = Stripe::client();
        try {
            $stripe->client->customers->delete($contact->stripe_customer_id);
        } catch (ApiErrorException) {
            abort(500);
        }
    }
}
