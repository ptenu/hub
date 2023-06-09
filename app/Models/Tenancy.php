<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenancy extends Model
{
    use SoftDeletes, HasUlids;

    public $timestamps = false;
    public $dates = [
        'start_date',
        'end_date',
        'notice_sent_on',
        'rent_changed_on',
        'gss_issued_on',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'uprn', 'uprn');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class);
    }

    public function getTypeNameAttribute(): string
    {
        $type  = $this->type;
        $map = [
            'ast' => 'Assured shorthold',
            'as'  => 'Assured',
            'flx' => 'Flexible',
            'pub' => 'Public sector',
            'reg' => 'Regulated (protected)',
            'emp' => 'Provided by employer',
            'mob' => 'Occupier of a mobile home',
            'lic' => 'Excluded or licence',
            ''    => 'Not known'
        ];

        return $map[$type];
    }
}
