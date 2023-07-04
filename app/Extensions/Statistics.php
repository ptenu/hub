<?php

namespace App\Extensions;

use Illuminate\Support\Facades\DB;

class Statistics
{
    public static function membershipStatuses()
    {
        $statistic = [];
        $result = DB::connection()->select('
            with statuses as (
                select membership_id,
                   status,
                   created_at,
                   rank() over (
                       partition by membership_id
                       order by created_at desc
                   ) as rank
            from membership_updates
            )

            select status, count(*) as count
            from statuses
            where rank = 1
            group by status;
        ');

        foreach ($result as $row) {
            $statistic[$row->status] = $row->count;
        }

        return $statistic;
    }
}
