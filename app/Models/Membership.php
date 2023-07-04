<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    protected $primaryKey = 'id';
    protected $keyType = 'string';

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

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)
                    ->ofMany('created_at', 'max');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(MembershipUpdate::class);
    }

    public function latestUpdate(): HasOne
    {
        return $this->hasOne(MembershipUpdate::class)
                    ->ofMany('created_at', 'max');
    }

    public function getBalance(Carbon $at = null): int
    {
        if (isNull($at)) {
            $at = Carbon::today();
        }

        $chargesTotal = Charge::query()
            ->where('membership_id', $this->id)
            ->where('date', '<=', $at)
            ->where('description', 'subs')
            ->sum('amount');

        $paymentsTotal = Payment::query()
            ->where('membership_id', $this->id)
            ->where('created_at', '<=', $at)
            ->where('status', 'succeeded')
            ->where('description', 'subs')
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

    // -----------------------------------------------------------------------------
    //  STATUS FUNCTIONS
    // -----------------------------------------------------------------------------

    protected function isStatusNew(Carbon $at = null): bool
    {
        if ($at == null) {
            $at = Carbon::now();
        }

        // False if contact was created more than four weeks ago
        return $this->created_at->addWeeks(4) > $at;
    }

    protected function isStatusRejected(Carbon $at = null): bool
    {
        if ($at == null) {
            $at = Carbon::now();
        }

        return $this->updates()
            ->where('status', 'rejected')
            ->whereBetween('created_at', [$this->created_at, $this->created_at->addWeeks(4)])
            ->exists();
    }

    protected function isStatusArrears(Carbon $at = null): bool
    {
        if ($at == null) {
            $at = Carbon::now();
        }
        $allowedArrears = 0 - $this->rate;
        return $this->getBalance($at) < $allowedArrears;
    }

    protected function isStatusLapsed(Carbon $at = null): bool
    {
        if ($at == null) {
            $at = Carbon::now();
        }
        $payments = $this->payments()
            ->where('status', 'succeeded')
            ->where('description', 'subs')
            ->whereBetween('created_at', [$at->subMonths(6), $at])
            ->exists();

        if ($payments == false && $this->isStatusArrears($at)) {
            return true;
        }

        return false;
    }

    protected function isStatusFailed(Carbon $at = null): bool
    {
        if ($at == null) {
            $at = Carbon::now();
        }

        if ($this->isStatusNew($at)) {
            return false;
        }

        return $this->payments()
            ->where('status', 'succeeded')
            ->where('description', 'subs')
            ->doesntExist();
    }

    public function status(Carbon $at = null): string
    {
        if ($at == null) {
            $at = Carbon::now();
        }

        if ($this->isStatusNew($at)) {
            return 'new';
        }

        if ($this->isStatusFailed($at)) {
            return 'failed';
        }

        if ($this->isStatusRejected($at)) {
            return 'rejected';
        }

        if ($this->isStatusLapsed($at)) {
            return 'lapsed';
        }

        if ($this->isStatusArrears($at))
        {
            return 'in-arrears';
        }

        return 'active';
    }

    public function getStatusAttribute(): string
    {
        return $this->latestUpdate->status;
    }
}
