<?php

namespace App\Jobs;

use App\Models\Charge;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMemberCharge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Membership $membership
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $membership = $this->membership;
        $today = Carbon::today();

        // Bail out if the member is non-member
        $status = $membership->status();
        if (in_array($status, ['failed', 'rejected', 'lapsed'])) {
            return;
        }

        // Don't create a new charge if, for some reason, they already
        // have a charge for this month (e.g. if they changed their payment date
        // to a date in the future).
        $existingCharge = $membership->charges()
            ->whereBetween('date', [$today->startOfMonth(), $today->endOfMonth()])
            ->exists();

        if ($existingCharge) {
            return;
        }

        $charge = new Charge();
        $charge->membership()->associate($membership);
        $charge->description = "subs";
        $charge->amount = $membership->rate;
        $charge->date = $today;
        $charge->save();
    }
}
