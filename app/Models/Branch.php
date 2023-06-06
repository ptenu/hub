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
        );
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
            ->select('contacts.*')
            ->has('membership')
            ->whereHas('residentialInterest', function (Builder $query) {
                $query->join(
                    '_address.addresses',
                    'property_interests.uprn',
                    'addresses.uprn')
                    ->join('branch_postcodes', 'branch_postcode.branch_id', $this->id)
                    ->where(
                        '_address.addresses.postcode',
                        'ilike',
                        DB::raw('"branch_postcodes"."postcode_substr" || \'%\'')
                    );
            });
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
