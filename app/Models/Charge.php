<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Charge extends Model
{
    use HasTimestamps;

    protected $dates = ['date'];

    public function contact(): HasOneThrough
    {
        return $this->hasOneThrough(Contact::class, Membership::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
