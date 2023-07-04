<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
    use HasTimestamps, SoftDeletes;

    public function officers(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'officers',
            'branch_id',
            'contact_id',
        )->withPivot('title', 'role', 'starts_on', 'ends_on');
    }

    public function addresses(): Builder
    {
        return Address::query()
            ->join('branch_postcodes', 'branch_postcodes.branch_id', '=', $this->id)
            ->where(
                'addresses.postcode',
                'ilike',
                DB::raw('"branch_postcodes"."postcode_substr" || \'%\'')
            );
    }

    public function getAddressesAttribute(): Collection
    {
        return $this->addresses()->get();
    }

    public function members(): Builder
    {
        return Contact::query()
            ->join('property_interests', 'contacts.id', '=', 'property_interests.contact_id')
            ->join('_address.addresses', 'addresses.uprn', '=', 'property_interests.uprn')
            ->join('branch_postcodes', 'addresses.postcode', 'like', DB::raw('"branch_postcodes"."postcode_substr" || \'%\''))
            ->join('branches', 'branches.id', '=', 'branch_postcodes.branch_id')
            ->whereRaw('( select status ' .
                           'from "membership_updates" mu ' .
                           'join "memberships" m on "mu"."membership_id" = "m"."id" ' .
                           'where "m"."contact_id" = "contacts"."id" ' .
                           'order by "mu"."created_at" desc ' .
                           'limit 1 ' .
                           ") in ('active', 'new', 'arrears')")
            ->whereIn('property_interests.type', ['occupier', 'owner-occupier', 'tenant', 'licensee'])
            ->whereNull('property_interests.deleted_at')
            ->whereRaw('"property_interests"."id" = ( ' .
                           'select "pb"."id" from "property_interests" pb ' .
                           'where "pb"."contact_id" = "contacts"."id" ' .
                           'order by "pb"."created_at" desc ' .
                           'limit 1 )')
            ->where('branches.id', $this->id);
    }

    public function getMembersAttribute(): Collection
    {
        return $this->members()->get();
    }

    public function getFullNameAttribute(): string
    {
        return $this->id . " / " . $this->name;
    }
}
