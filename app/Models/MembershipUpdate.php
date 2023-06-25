<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipUpdate extends Model
{
    use HasTimestamps;

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
