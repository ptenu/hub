<?php

namespace App\Http\Controllers;

use App\Extensions\Stripe;
use App\Extensions\TwillioService;
use App\Mail\VerifyLink;
use App\Models\Address;
use App\Models\Email;
use App\Models\PropertyInterest;
use App\Models\TelephoneNumber;
use App\Models\Tenancy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PrinsFrank\Standards\Language\LanguageAlpha2;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return view('account.index', [
            "user" => $request->user()
        ]);
    }

    public function dashboard(Request $request)
    {
        return view('dashboard', [
            "user" => $request->user()
        ]);
    }

    public function updateAddress(Request $request)
    {
        $postcode = $request->query("postcode", false);
        if (!$postcode) {
            return view('account.change-address');
        }

        $postcode = $request->input("postcode.0") . ' ' . $request->input('postcode.1');
        $postcode = strtoupper($postcode);

        $addresses = Address::inPostcode($postcode);

        return view('account.change-address', [
            "addresses" => $addresses,
            "postcode" => [$request->input("postcode.0"), $request->input('postcode.1')]
        ]);
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'uprn' => ['required']
        ]);

        $uprn = $request->input('uprn');
        $user = $request->user();

        $interest = PropertyInterest::query()->firstOrCreate([
            'contact_id' => $user->id,
            'uprn' => $uprn
        ]);

        $interest->deleted_at = null;
        $interest->save();

        return view('account.interest-details', ['interest' => $interest]);
    }

    public function saveInterest(Request $request)
    {
        $request->validate([
            'interest_id' => ['required'],
            'moved_in.day' => ['required'],
            'moved_in.month' => ['required'],
            'moved_in.year' => ['required'],
            'tenure' => ['required']
        ]);

        PropertyInterest::query()
            ->where('contact_id', $request->user()->id)
            ->where('id', '!=', $request->input('interest_id'))
            ->delete();

        $interest = PropertyInterest::query()
            ->where('id', $request->input('interest_id'))
            ->where('contact_id', $request->user()->id)
            ->firstOrFail();
        $interest->type = $request->input('tenure');
        $move_in_date = Carbon::create(
            $request->input('moved_in.year'),
            $request->input('moved_in.month'),
            $request->input('moved_in.day'),
        );

        $interest->created_at = $move_in_date;
        $interest->save();

        session()->flash('status', 'Your address has been updated');

        return redirect('/account');
    }

    public function detailsForm(Request $request)
    {
        return view('account.update-details', [
            'user' => $request->user(),
            'languages' => LanguageAlpha2::cases()
        ]);
    }

    public function saveDetails(Request $request)
    {
        $request->validate([
            'given_name' => 'required',
            'family_name' => 'required',
        ]);

        $contact = $request->user();

        // Update name if it doesn't match the existing one
        if ($contact->given_name != $request->input('given_name')) {
            $contact->given_name = ucwords($request->input('given_name'));
        }
        if ($contact->given_name != $request->input('given_name')) {
            $contact->given_name = ucwords($request->input('given_name'));
        }

        // Update date of birth, if provided
        $year = $request->input('date_of_birth.year');
        $month = $request->input('date_of_birth.month');
        $day = $request->input('date_of_birth.day');
        if ($year != '' && $month != '' && $day != '') {
            $newDate = Carbon::create($year, $month, $day);
            $contact->date_of_birth = $newDate;
        }

        // Update legal sex (or set to null)
        $sexKey = $request->input('legal_sex');
        $newSex = null;
        if (in_array($sexKey, ['M', 'F'])) {
            $newSex = $sexKey;
        }
        $contact->legal_sex = $newSex;

        // Update first language
        $newLanguageCode = $request->input('first_language');
        if ($newLanguageCode == '') {
            $newLanguageCode = null;
        }
        $contact->first_language = $newLanguageCode;

        // Save
        $contact->save();

        $request->session()->flash('status', 'Your details have been updated.');
        return redirect('/account');
    }

    public function viewTenancies(Request $request)
    {
        $contact = $request->user();
        $tenancy = $contact->current_tenancy;

        return view('account.tenancy', [
            'user' => $contact,
            'tenancy' => $tenancy
        ]);
    }

    public function updateTenancy(Request $request)
    {
        $contact = $request->user();
        $tenancy = $contact->current_tenancy;

        return view('account.update-tenancy', [
            'user' => $contact,
            'tenancy' => $tenancy
        ]);
    }

    public function saveTenancy(Request $request)
    {
        $contact = $request->user();

        // Try to find existing matching tenancy record
        $tenancy = $contact->current_tenancy;

        // If no match, create a new one
        if (!$tenancy) {
            $tenancy = new Tenancy();
            $tenancy->address()->associate($contact->residentialInterest->address);
            $tenancy->save();
        }

        // Set each attribute
        if (!$tenancy->contacts()->where('contact_id', $contact->id)->exists()) {
            $tenancy->contacts()->attach($contact, ['role' => 'tenant']);
        }


        // Set start date
        if ($request->input('start_date.day')
            && $request->input('start_date.month')
            && $request->input('start_date.year')) {
            $tenancy->start_date = Carbon::create(
                $request->input('start_date.year'),
                $request->input('start_date.month'),
                $request->input('start_date.day')
            );
        }

        // Set type
        if ($request->input('type')) {
            $tenancy->type = $request->input('type');
        }

        // Set is_hmo
        if (!is_null($request->input('hmo'))) {
            $tenancy->is_hmo = $request->input('hmo');
        }

        // Set rent amount
        if ($request->input('rent_amount')) {
            $tenancy->rent_amount = $request->input('rent_amount');
        }

        // Set rent period
        if ($request->input('rent_period')) {
            $tenancy->rent_period = $request->input('rent_period');
        }

        // Set term details
        if ($request->input('initial_length')) {
            $tenancy->initial_length = $request->input('initial_length');
        }
        if ($request->input('not_fixed_term')) {
            $tenancy->initial_length = 0;
        }

        // Set deposit amount
        if ($request->input('deposit_amount') != null) {
            $tenancy->deposit_amount = $request->input('deposit_amount');
        }

        // Deposit scheme details
        $tenancy->dps_status = 'unknown';
        if ($request->input('dps_name')) {
            $tenancy->dps_name = $request->input('dps_name');
            $tenancy->dps_status = 'protected';
        }
        if ($request->input('dps_reference')) {
            $tenancy->dps_reference = $request->input('dps_reference');
            $tenancy->dps_status = 'protected';
        }

        // Required documentation
        if ($request->input('eps_issued')) {
            $tenancy->eps_issued = true;
        }
        if ($request->input('htr_issued')) {
            $tenancy->htr_issued = true;
        }
        if ($request->input('dps_status') == 'issued') {
            $tenancy->dps_status = 'issued';
        }

        $tenancy->save();

        session()->flash('status', 'Your tenancy details have been updated');

        return redirect('/account/tenancy');
    }

    public function membership(Request $request)
    {
        return view('account.membership', ['user' => $request->user()]);
    }

    public function getPaymentMethod(Request $request)
    {
        $stripe = Stripe::client();
        $contact = $request->user();
        $customer_id = Stripe::getOrCreateCustomer($contact);

        $setupIntent = $stripe->client->setupIntents->create([
            'customer' => $customer_id,
            'usage' => 'off_session',
            'automatic_payment_methods' => [
                'enabled' => 'true'
            ]
        ]);

        return view('account.change-payment-method', [
            'user' => $request->user(),
            'client_secret' => $setupIntent['client_secret'],
            'stripe_pub_key' => env('STRIPE_PUBLIC_KEY')
        ]);
    }

    public function rate(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('account.change-rate', [
                'user' => $request->user()
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|between:3,18'
        ]);

        $amount = $request->input('amount') * 100;
        $membership = $request->user()->membership;
        $membership->rate = $amount;
        $membership->save();

        session()->flash('status', 'Your payment amount has been updated.');

        return redirect('/account/membership');
    }

    public function paymentDay(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('account.change-payment-day', [
                'user' => $request->user()
            ]);
        }

        $request->validate([
            'day' => 'required|numeric|between:1,31'
        ]);

        $request->user()->membership->payment_day = $request->input('day');
        $request->user()->membership->save();

        return redirect('/account/membership');
    }

    public function getContactPreferences(Request $request)
    {
        return view('account.contact-details', [
            'user' => $request->user()
        ]);
    }

    public function addEmail(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('account.new-email', [
                'user' => $request->user()
            ]);
        }

        $request->validate([
            "email" => "required|email|unique:emails,address"
        ]);

        $email = new Email();
        $email->address = $request->input('email');
        $email->contact_id = $request->user()->id;

        // Handle consent codes
        if ($request->has('consent')) {
            $consentString = "";
            foreach ($request->consent as $code) {
                $consentString = $consentString . strtoupper($code);
            }
            $email->consent_codes = $consentString;
        }

        $email->save();

        // Send verification link
        $token = bin2hex(random_bytes(8));
        $endpoint = Crypt::encryptString($email->address);
        $verifyUrl = route('verify-email', [$endpoint, $token]);

        DB::table('verification_codes')->insert([
            'endpoint' => $email->address,
            'sent_at' => Carbon::now(),
            'sent_via' => 'email',
            'token' => $token
        ]);

        Mail::to($email->address)->send(new VerifyLink($verifyUrl));

        session()->flash('status', "The email address has been added, please check your inbox (and spam folders) for a verification email.");
        return redirect()->route('account.contact');
    }

    public function addTelephone(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('account.new-telephone', [
                'user' => $request->user()
            ]);
        }

        $twillio = TwillioService::getClient();

        $request->validate([
            'number' => 'required|starts_with:01,02,07|size:11|unique:telephone_numbers,number'
        ]);

        $internationalNumber = "+44" . substr($request->input('number'), 1);

        // Perform number lookup
        $lookup = $twillio->lookups->v2->phoneNumbers($internationalNumber)
                                       ->fetch([
                                           "fields" => "line_type_intelligence"
                                       ]);

        if ($lookup->valid !== true) {
            session()->flash('error', 'The phone number you entered was not valid.');
            return redirect()->route('account.contact');
        }

        if (in_array(
            $lookup->lineTypeIntelligence['type'],
            ['premium', 'sharedCost', 'uan', 'pager', 'voicemail', 'tollFree']
        )) {
            session()->flash('error', 'Unfortunately, the type of number you tried to add is not supported.');
            return redirect()->route('account.contact');
        }

        // Create number
        $number = new TelephoneNumber();
        $number->contact_id = $request->user()->id;
        $number->number = $lookup->phoneNumber;
        $number->national_number = $lookup->nationalFormat;
        $number->type = $lookup->lineTypeIntelligence['type'];
        $number->carrier = $lookup->lineTypeIntelligence['carrier_name'];
        $number->sms_enabled = $number->type === "mobile";

        // Handle consent codes
        if ($request->has('consent')) {
            $consentString = "";
            foreach ($request->consent as $code) {
                $consentString = $consentString . strtoupper($code);
            }
            $number->consent_codes = $consentString;
        }

        // Save number
        $number->save();

        // Perform verification call
        $verification = $twillio->verify->v2->services(env('VERIFY_SERVICE_ID'))
            ->verifications
            ->create(
                $number->number,
                $number->sms_enabled ? "sms" : "call"
            );

        return view('account.verify', [
            'user' => $request->user(),
            'endpoint' => $number->number,
            'via' => $number->sms_enabled ? "sms" : "call"
        ]);
    }

    public function verify(Request $request) {
        $request->validate([
            'endpoint' => 'required',
            'via' => 'required',
            'code' => 'required'
        ]);

        if ($request->input('via') === "sms" || $request->input('via') === "call") {
            $twillio = TwillioService::getClient();
            $check = $twillio->verify->v2->services(env('VERIFY_SERVICE_ID'))
                                         ->verificationChecks
                                         ->create([
                                             "to" => $request->input('endpoint'),
                                             'code' => $request->input('code')
                                         ]);
            if ($check->status !== "approved") {
                session()->flash('error', "It wasn't possible to verify your new number.");
                return redirect()->refresh();
            }
                $number = TelephoneNumber::query()
                    ->where('number', $check->to)
                    ->where('contact_id', $request->user()->id)
                    ->firstOrFail();
                $number->verified_at = Carbon::now();
                $number->save();
                session()->flash('status', 'Your new telephone number has been verified and added.');
                return redirect()->route('account.contact');
        }
    }

    public function verifyToken(Request $request, $endpoint, $token)
    {
        $emailAddress = Crypt::decryptString($endpoint);
        $tokenValid = DB::table('verification_codes')
            ->where('endpoint', $emailAddress)
            ->where('sent_at', '>', Carbon::now()->subHours(1))
            ->where('token', $token)
            ->exists();

        if (!$tokenValid) {
            session()->flash("error", "The verification link was not valid.");
        }
        else {
            $email = Email::query()->where('address', $emailAddress)->firstOrFail();
            $email->verified_at = Carbon::now();
            $email->save();
            session()->flash('status', "Your email was verified successfully.");
        }

        if (Auth::guest()) {
            return redirect('/');
        }

        return redirect()->route('account.contact');

    }

    public function deleteTelephone(Request $request, TelephoneNumber $number) {
        if ($number->contact->id != $request->user()->id) {
            session()->flash('error', 'You are not allowed to delete that telephone number.');
            return redirect()->route('account.contact');
        }

        session()->flash('status', 'The number ' . $number->normalisedNumber() . ' was deleted.');
        $number->delete();

        return redirect()->route('account.contact');
    }

    public function deleteEmailAddress(Request $request, Email $email) {
        if ($email->contact->id != $request->user()->id) {
            session()->flash('error', 'You are not allowed to delete that email address.');
            return redirect()->route('account.contact');
        }

        if ($request->user()->emails()->count() == 1) {
            session()->flash('error', 'You cannot delete this email address, as you must have at least one on record.');
            return redirect()->route('account.contact');
        }

        session()->flash('status', 'The email address ' . $email->address . ' was deleted.');
        $email->delete();

        return redirect()->route('account.contact');
    }
}

