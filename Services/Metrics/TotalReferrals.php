<?php

namespace Modules\Rank\Services\Metrics;

use Modules\Rank\Services\MetricInterface;
use Modules\Rank\Services\MetricContext;
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

    /**
     * Resolve must NOT run global aggregates.
     * Uses MetricContext->totalReferralCommissions for global totals.
     */
    public function resolve(User $user, ?MetricContext $context = null): float
    {
        if (!function_exists('moduleExists') || !moduleExists('Referral')) {
            return 0;
        }

        try {
            if (!class_exists(\Modules\Referral\Entities\Referral::class)) {
                return 0;
            }

            $total_commissions = $context?->totalReferralCommissions ?? 0.0;

            // Per-user referrals only
            $userReferralsQuery = \Modules\Referral\Entities\Referral::with(['referrer', 'referredUser'])
                ->where('referrer_user_id', $user->id)
                ->whereHas('referredUser', function ($query) {
                    $query->whereNotNull('id');
                })->whereHas('referrer', function ($query) {
                    $query->whereNotNull('id');
                });

            $total_users_commission = (float) $userReferralsQuery->where('status', 'approved')->sum('commission_amount') ?? 0.0;
            $referrals_count = (int) $userReferralsQuery->count();

            $percent = 0.0;
            if ($total_commissions > 0.0) {
                $percent = ($total_users_commission / $total_commissions) * 100.0;
            }

            return (float) $referrals_count + $percent;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
