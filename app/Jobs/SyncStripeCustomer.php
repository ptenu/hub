<?php

namespace App\Jobs;

use App\Extensions\Stripe;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncStripeCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
       public Contact $contact
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $stripe = Stripe::client();
        $contact = $this->contact;
        $stripe->client->customers->update($contact->stripe_customer_id, [
            'email' => $contact->email ? $contact->email->address : null,
            'name' => $contact->full_name,
            'phone' => $contact->telephoneNumber ? $contact->telephoneNumber->number : null,
        ]);
    }
}
