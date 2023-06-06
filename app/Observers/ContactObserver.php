<?php

namespace App\Observers;

use App\Extensions\Stripe;
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
        if (is_null($contact->stripe_customer_id)) {
            return;
        }

        $stripe = Stripe::client();
        $stripe->client->customers->update($contact->stripe_customer_id, [
            'email' => $contact->email ? $contact->email->address : null,
            'name' => $contact->full_name,
            'phone' => $contact->telephoneNumber ? $contact->telephoneNumber->number : null,
        ]);
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
