<?php

namespace App\Extensions;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class Stripe
{

    public function __construct(
        public StripeClient $client
    )
    {
    }

    public static function client(): Stripe
    {
        $client = new StripeClient(env('STRIPE_PRIVATE_KEY'));
        return new Stripe($client);
    }

    public static function getOrCreateCustomer(Contact $contact): string
    {
        $stripe = Stripe::client();

        if ($contact->stripe_customer_id) {
            return $contact->stripe_customer_id;
        }

        // No customer ID - does the email match a customer?
        $customer = $stripe->client->customers->search([
            'query' => 'email:\'' . $contact->email . '\''
        ]);

        // If it does, update the contact record
        // and return the ID.
        if (count($customer['data']) > 0) {
            $customer = $customer['data'][0];
            $contact->stripe_customer_id = $customer['id'];
            $contact->save();
            return $customer['id'];
        }

        // No match - create a new customer on Stripe
        // Get Address
        $address = DB::connection('addresses')
            ->table('addresses_a')
            ->select('addresses_a.*')
            ->where('uprn', $contact->residentialInterest()->uprn)
            ->first();

        // Create the customer
        $customer = $stripe->client->customers->create([
            'name' => $contact->full_name,
            'email' => $contact->email,
            'address' => [
                'line1' => $address['sao'] ?? $address['pao'],
                'line2' => $address['sao'] != null ? $address['pao'] : null,
                'city' => $address['district'] ?? $address['down_name'],
                'postal_code' => $address['postcode']
            ],
            'metadata' => [
                'membership_number' => $contact->membership->id,
                'source' => 'account.change-payment-method',
                'address_uprn' => $address->uprn
            ]
        ]);

        // Update the contact record
        $contact->stripe_customer_id = $customer['id'];
        $contact->save();
        return $customer['id'];

    }
}
