<?php

namespace App\Jobs;

use App\Models\Membership;
use App\Models\MembershipUpdate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersistMembershipStatus implements ShouldQueue
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
        $currentUpdateStatus = $this->membership->status;
        $currentLiveStatus   = $this->membership->status();

        if ($currentLiveStatus == $currentUpdateStatus) {
            return;
        }

        $newUpdate = new MembershipUpdate();
        $newUpdate->status = $currentLiveStatus;
        $newUpdate->membership_id = $this->membership->id;
        $newUpdate->save();
    }
}
