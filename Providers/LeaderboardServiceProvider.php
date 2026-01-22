<?php

use Illuminate\Support\Facades\Event;
use Modules\Referral\Events\ReferralCommissionCredited;
use Modules\Leaderboard\Listeners\UpdateReferralEarningsLeaderboard;
use Illuminate\Support\ServiceProvider;


class DummyPluginServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(
            ReferralCommissionCredited::class,
            [UpdateReferralEarningsLeaderboard::class, 'handle']
        );
    }
}
