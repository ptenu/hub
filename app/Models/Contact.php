<?php

namespace App\Models;

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
        return $this->hasOne(Membership::class);
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
            ->wherePivotIn('role', ['tenant', 'occupier']);
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
        $this->remember_token = $value;
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
}
