<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
use function Aws\filter;

class Address extends Model
{
    protected $primaryKey = "uprn";
    protected $connection = "addresses";

    public function contacts(): HasManyThrough
    {
        return $this->hasManyThrough(Contact::class, PropertyInterest::class);
    }

    public function propertyInterests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class);
    }

    public function tenancies(): BelongsToMany
    {
        return $this->belongsToMany(Tenancy::class);
    }

    public function getAddressArray(): array
    {
        $address = DB::connection('addresses')
            ->table('addresses_a')
            ->select('organisation', 'tao', 'sao', 'pao', 'locality', 'town_name', 'district', 'postcode')
            ->where("uprn", '=', $this->uprn)
            ->first();

        if (!$address) {
            return [];
        }
        $addressParts = [
            $address->organisation,
            $address->tao,
            $address->sao,
            $address->pao,
            $address->locality,
            $address->town_name,
            $address->district,
            $address->postcode
        ];

        $addressParts = array_filter($addressParts, function($v) {
            return isset($v);
        });

        return $addressParts;
    }

    public function getMultiLineAddress(): string
    {
        return join("\n", $this->getAddressArray());
    }

    public function getSingleLineAddress(): string
    {
        return join(", ", $this->getAddressArray());
    }

    public function getClassificationAttribute(): string
    {
        $code = DB::connection('addresses')
            ->table('codes')
            ->where('group', 'ClassificationCodes')
            ->where('code', $this->classification_code)
            ->first();

        return $code->description;
    }

    public static function inPostcode($postcode)
    {
        return DB::connection('addresses')
            ->table('addresses_a')
            ->select('addresses_a.*')
            ->join('addresses', 'addresses.uprn', 'addresses_a.uprn')
            ->where('addresses.classification_code', 'like', 'R%')
            ->where('addresses_a.postcode', '=', $postcode)
            ->orderBy('addresses.pao_start_number')
            ->orderBy('addresses.pao_start_suffix')
            ->orderBy('addresses.pao_text')
            ->orderBy('addresses.sao_start_number')
            ->orderBy('addresses.sao_text')
            ->get();
    }
}
