<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class Membership extends Model
{
    use HasTimestamps, SoftDeletes;

    protected $fillable = [
        'id',
        'contact_id',
        'type',
        'rate',
        'payment_day',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(MembershipUpdate::class);
    }

    public function currentUpdate(): HasOne
    {
        return $this->hasOne(MembershipUpdate::class)->ofMany('created_at', 'max');
    }

    public function getStatusAttribute(): string
    {
        return $this->currentUpdate->status;
    }

    public function getBalance($at = null): int
    {
        if (isNull($at)) {
            $at = Carbon::today();
        }

        $chargesTotal = Charge::query()
            ->where('membership_id', $this->id)
            ->where('date', '<=', $at)
            ->sum('amount');

        $paymentsTotal = Payment::query()
            ->where('membership_id', $this->id)
            ->where('created_at', '<=', $at)
            ->sum('amount');

        return $paymentsTotal - $chargesTotal;
    }

    public function generateMembershipNumber(): string
    {
        $letters     = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        $today       = $this->created_at;
        $monthLetter = $letters[$today->month - 1];
        $yearCode    = dechex($today->year);
        $suffix      = random_int(1000, 9999);
        return $monthLetter . $yearCode . $suffix;
    }
}
