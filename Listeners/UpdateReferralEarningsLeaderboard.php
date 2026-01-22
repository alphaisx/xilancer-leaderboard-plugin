<?php

namespace Modules\Leaderboard\Listeners;

use Modules\Referral\Events\ReferralCommissionCredited;

class UpdateReferralEarningsLeaderboard
{
    public function handle(ReferralCommissionCredited $event)
    {
        // Update leaderboard score for $event->referrerId
        // Increment 'referral_earnings' metric by $event->amount
    }
}
