<?php

namespace App\Jobs;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCharges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::today();
        $daysToProcess = [];
        $daysToProcess[] = $today->day;

        // Don't process on weekends
        if ($today->isWeekend()) {
            return;
        }

        // If it's a Friday, also process the next two days (i.e. Sat and Sun)
        if ($today->isFriday()) {
            array_push($daysToProcess,
                $today->addDay()->day,
                $today->addDays(2)->day,
            );
        }

        // If it's the end of the month today (or on Fridays, if the
        // month ends over the weekend) add any remaining dates between
        // today and the 31st, to include members who have payment days later
        // than the end of this month.
        $lastDayOfMonth = $today->endOfMonth()->day;
        if (in_array($lastDayOfMonth, $daysToProcess)) {
            for ($x = $lastDayOfMonth; $x <= 31; $x++) {
                $daysToProcess[] = $x;
            }
        }

        $memberships = Membership::query()->where('take_payments', true);

        // If it is the last day of the month, we need to capture any
        // memberships that, for whatever reason, hasn't had a payment this month
        // (e.g. because they changed their payment day to a day in the past).
        if (in_array($lastDayOfMonth, $daysToProcess)) {
            $memberships->where(function (Builder $query) use ($daysToProcess) {
                $query
                    ->whereIn('payment_day', $daysToProcess)
                    ->orWhereHas('charges', function (Builder $chargesQuery) {
                        $chargesQuery->whereBetween('date', [
                            Carbon::today()->startOfMonth(),
                            Carbon::today()->endOfMonth(),
                        ]);
                    }, '<');
            });
        } else {
            // Otherwise, just get today's memberships.
            $memberships->whereIn('payment_day', $daysToProcess);
        }

        foreach ($memberships->get() as $membership) {
            CreateMemberCharge::dispatch($membership);
        }
    }
}
