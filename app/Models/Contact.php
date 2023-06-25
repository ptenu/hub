<?php

namespace App\Models;

use App\Extensions\Stripe;
use App\Observers\ContactObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PrinsFrank\Standards\Language\LanguageAlpha2;

class Contact extends Authenticatable
{
    use HasFactory, HasTimestamps, HasUlids;

    protected $dates = [
        'date_of_birth'
    ];

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function email(): HasOne
    {
        return $this->hasOne(Email::class)->ofMany('priority', 'max');
    }

    public function telephoneNumbers(): HasMany
    {
        return $this->hasMany(TelephoneNumber::class);
    }

    public function telephoneNumber(): HasOne
    {
        return $this->hasOne(TelephoneNumber::class)->ofMany('priority', 'max');
    }

    public function membership(): HasOne
    {
        return $this->hasOne(Membership::class)->ofMany('created_at', 'MAX');
    }

    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(Document::class, Version::class);
    }

    public function addresses(): HasManyThrough
    {
        return $this->hasManyThrough(Address::class, PropertyInterest::class);
    }

    public function propertyInterests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class);
    }

    public function residentialInterest(): HasOne
    {
         return $this->hasOne(PropertyInterest::class)->ofMany([
                 'created_at' => 'max'
         ], function(Builder $query) {
             $query->whereIn('type', [
                 'occupier',
                 'owner-occupier',
                 'tenant',
                 'licensee'
             ]);
         });

    }

    public function tenancies(): BelongsToMany
    {
        return $this->belongsToMany(Tenancy::class)
            ->wherePivotIn('role', ['tenant', 'occupier'])
            ->orderBy('start_date', 'desc');
    }

    public function previousTenancies(): BelongsToMany
    {
        return $this->belongsToMany(Tenancy::class)
            ->wherePivotIn('role', ['tenant', 'occupier'])
            ->where('end_date', '<', Carbon::now())
            ->where('end_date', '!=', null)
            ->orderBy('start_date', 'desc');
    }

    public function branch(): Builder
    {
        return Branch::query()
            ->select('branches.*')
            ->join('branch_postcodes', 'branch_postcodes.branch_id', 'branches.id')
            ->join(
                '_address.addresses',
                'addresses.postcode',
                'ilike',
                DB::raw('"branch_postcodes"."postcode_substr" || \'%\'')
            )
            ->join(
                'property_interests',
                'property_interests.uprn',
                '_address.addresses.uprn')
            ->where('property_interests.contact_id', $this->id)
            ->where('property_interests.deleted_at', null)
            ->whereIn('type', [
                'occupier',
                'owner-occupier',
                'tenant',
                'licensee'
            ])
            ->orderByRaw('length("branch_postcodes"."postcode_substr") desc');
    }

    public function getBranchAttribute(): ?Branch
    {
        return $this->branch()->first();
    }

    public function getCurrentTenancyAttribute(): ?Tenancy
    {
        if ($this->residentialInterest == null) {
            return null;
        }

        return $this->tenancies()
            ->where(function($query) {
              $query->where('end_date', '=', null)->orWhere('end_date', '>', Carbon::now());
            })
            ->where('start_date', '<', Carbon::now())
            ->where('uprn', '=', $this->residentialInterest->uprn)
            ->orderBy('start_date', 'desc')
            ->first();
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->given_name . " " . $this->family_name;
        return ucwords($name);
    }

    public function legalName(): string
    {
        return strtoupper($this->family_name) . ', ' . ucfirst($this->given_name);
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->attributes[$this->getAuthIdentifierName()];
    }

    public function getAuthPassword()
    {
        $session = Session::getCurrrentSession();
        return $session->password_hash;
    }

    public function setAuthPassword(string $password): void
    {
        $session = Session::getCurrrentSession();
        $session->password_hash = Hash::make($password);
        $session->save();

        session()->increment('password_count');
        session(['password_sent' => Carbon::now()]);
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->attributes[$this->getRememberTokenName()] = $value;
        $this->save();
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getLanguageName()
    {
        if ($this->first_language == null) {
            return null;
        }
        $language = LanguageAlpha2::from($this->first_language);
        return $language->toLanguageName();
    }

    public function getPaymentMethod(): ?array
    {
        $stripe = Stripe::client();

        if (!$this->stripe_customer_id)
        {
            return null;
        }

        $paymentMethodId = $this->membership->payment_method_id;

        if (!$paymentMethodId) {
            // Try getting default
            $customer = $stripe->client->customers->retrieve($this->stripe_customer_id);
            $paymentMethodId = $customer['invoice_settings']['default_payment_method'];

            if (!$paymentMethodId) {
                // No default so get from last payment
                if ($this->membership->payments()->exists()) {
                    $mostRecentPayment = $this->membership->payments()
                        ->where('stripe_payment_method', '!=', null)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($mostRecentPayment) {
                        $paymentMethodId = $mostRecentPayment->stripe_payment_method;
                    }
                }
            }

            $this->membership->payment_method_id = $paymentMethodId;
            $this->membership->save();
        }

        if (!$paymentMethodId) {
            return null;
        }

        $pm = $stripe->client->paymentMethods->retrieve($paymentMethodId);

        $details = [
            'type' => $pm['type']
        ];

        if ($details['type'] == 'card') {
            $details['brand'] = $pm['card']['brand'];
            $details['last4'] = $pm['card']['last4'];
            $details['sort_code'] = str_pad($pm['card']['exp_month'], 2, "0", STR_PAD_LEFT)
                . '/'
                . substr($pm['card']['exp_year'], 2);
        }

        if ($details['type'] == 'bacs_debit') {
            $details['last4'] = $pm['bacs_debit']['last4'];
            $details['sort_code'] = $pm['bacs_debit']['sort_code'];
        }

        return $details;

    }
}
