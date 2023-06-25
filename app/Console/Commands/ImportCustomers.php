<?php

namespace App\Console\Commands;

use App\Extensions\Stripe;
use App\Models\Address;
use App\Models\Charge;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Membership;
use App\Models\MembershipUpdate;
use App\Models\Payment;
use App\Models\PropertyInterest;
use App\Models\TelephoneNumber;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class ImportCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customers from stripe and ensure all data is stored correctly.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stripe = Stripe::client();

        // First, get array of all customers
        $this->line('Importing data from stripe...');
        $customers = $stripe->client->customers->all(['limit' => 100]);

        foreach ($customers['data'] as $customer) {
            if ($customer['email'] == null) {
                continue;
            }

            $contact = Auth::getProvider()->retrieveByCredentials(['email' => $customer['email']]);

            $membership_type = $customer['metadata']['initial_membership_type'] ?? null;

            // Update stripe ID if customer exists, and skip processing
            if ($contact) {
                $contact->stripe_customer_id = $customer["id"];
                $contact->save();
                $this->line('Skipping: ' . $customer['name'] . ' <' . $customer['email'] . '>');
                continue;
            }

            $this->line('Importing: ' . $customer['name'] . ' <' . $customer['email'] . '>');

            // Create new contact
            $contact = new Contact();
            $contact->given_name = trim(explode(" ", $customer["name"])[0]) ?? "";
            $contact->family_name = trim(explode(" ", $customer["name"])[1]) ?? "";
            $contact->stripe_customer_id = $customer["id"];
            $contact->save();

            // Add email address
            $email = new Email();
            $email->contact()->associate($contact);
            $email->address = $customer['email'];
            $email->disabled = false;
            $email->verified_at = Carbon::now();
            $email->consent_codes = "NEMCIR";
            $email->save();

            // Add phone number
            if ($customer['phone']) {
                $phone = new TelephoneNumber();
                $phone->contact()->associate($contact);
                $phone->number = $customer['phone'];
                $phone->priority = 0;
                $phone->disabled = false;
                $phone->verified_at = Carbon::now();
                $phone->consent_codes = "R";
                $phone->save();

            }

            // Attempt address match
            $address = Address::query()
                ->where('addresses.postcode', $customer['address']['postal_code'])
                ->join("addresses_a", 'addresses.uprn', '=', 'addresses_a.uprn')
                ->where('addresses_a.pao', 'ilike', $customer['address']['line1'])
                ->first();

            if ($address) {
                $pi = new PropertyInterest();
                $pi->contact()->associate($contact);
                $pi->uprn = $address->uprn;
                $pi->type = $membership_type == 'STANDARD' ? "tenant" : 'occupier';
                $pi->created_at = Carbon::createFromTimestamp($customer['created']);
                $pi->save();
            }

            // Get payments and add membership information
            $payments = $stripe->client->paymentIntents->all([
                'limit' => 100,
                'customer' => $customer["id"]
            ]);

            // Create membership
            $membership = new Membership();
            $membership->created_at = Carbon::createFromTimestamp($customer['created']);
            $membership->contact()->associate($contact);

            // Generate Membership number
            $membershipNumber = $customer['metadata']['membership_number'] ?? $membership->generateMembershipNumber();
            $membership->type = $membership_type == 'STANDARD' ? "O" : 'A';
            $membership->id = $membershipNumber;

            if (count($payments['data']) > 0)
            {
                $firstPayment = $payments['data'][0];
                $membership->rate = $firstPayment['amount'];
                $membership->payment_day = Carbon::createFromTimestamp($firstPayment['created'])->day;
                $membership->take_payments = true;
                $membership->save();
            }

            $update_joined = new MembershipUpdate();
            $update_joined->membership_id = $membershipNumber;
            $update_joined->description = 'joined';
            $update_joined->created_at = Carbon::createFromTimestamp($firstPayment['created']);
            $update_joined->status = $membership->status($update_joined->created_at);
            $update_joined->save();

            foreach ($payments['data'] as $p) {
                // Create payment
                $payment = new Payment();
                $payment->membership_id = $membershipNumber;
                $payment->description = 'subs';
                $payment->amount = $p['amount'];
                $payment->status = $p['status'];
                $payment->stripe_payment_intent = $p['id'];
                $payment->stripe_payment_method = $p['payment_method'];
                $payment->created_at = Carbon::createFromTimestamp($p['created']);
                $payment->save();

                // Create associated charge
                $charge = new Charge();
                $charge->membership_id = $membershipNumber;
                $charge->amount = $p['amount'];
                $charge->created_at = Carbon::createFromTimestamp($p['created'])->day(1);
                $charge->date = Carbon::createFromTimestamp($p['created'])->day(1);
                $charge->description = 'subs';
                $charge->save();
            }
        }

        return Command::SUCCESS;
    }
}
