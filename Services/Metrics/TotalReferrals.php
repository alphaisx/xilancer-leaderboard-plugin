<?php

namespace Modules\Leaderboard\Services\Metrics;

use Modules\Leaderboard\Services\MetricInterface;
use App\Models\User;

class TotalReferrals implements MetricInterface
{
    public function key(): string
    {
        return 'total_referrals';
    }
    public function label(): string
    {
        return 'Total Referrals';
    }
    public function weight(): float
    {
        return 0.5;
    }

    public function resolve(User $user): float
    {
        if (!function_exists('moduleExists') || !moduleExists('Referral')) {
            return 0;
        }

        try {
            // Assume Referral module exposes a Referral model
            if (!class_exists(\Modules\Referral\Entities\Referral::class)) {
                return 0;
            }
            // Overall Table Calculation
            $total_commissions = \Modules\Referral\Entities\Referral::whereNot('referrer_user_id', null)->where('status', 'approved')->whereNot('commission_processed_at', null)->sum('commission_amount') ?? 0;

            // Users Calculation
            $total_referrals = \Modules\Referral\Entities\Referral::with(['referrer', 'referredUser'])->where('referrer_user_id', $user->id)->whereHas('referredUser', function ($query) {
                $query->whereNotNull('id');
            })->whereHas('referrer', function ($query) {
                $query->whereNotNull('id');
            });
            $total_users_commission = $total_referrals->where('status', 'approved')->sum('commission_amount') ?? 0;
            $percent = ($total_users_commission / $total_commissions) * 100;
            // dd($this->label(), $total_commissions, $total_users_commission, $percent);
            return (float) $total_referrals->count() + $percent;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
