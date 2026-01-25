<?php

namespace Modules\Rank\Services;

use App\Models\Order;

class MetricContext
{
    public float $totalCompletedOrdersAmount = 0.0;
    public float $totalReferralCommissions = 0.0;

    public function __construct(array $data = [])
    {
        $this->totalCompletedOrdersAmount = $data['totalCompletedOrdersAmount'] ?? 0.0;
        $this->totalReferralCommissions = $data['totalReferralCommissions'] ?? 0.0;
    }

    /**
     * Build the context by computing global aggregates once.
     */
    public static function build(): self
    {
        $totalOrdersAmt = 0.0;
        $totalRefCommissions = 0.0;

        // Completed orders global sum (guard class existence)
        try {
            if (class_exists(\App\Models\Order::class)) {
                $totalOrdersAmt = (float) \App\Models\Order::where('status', 3)
                    ->where('payment_status', 'complete')
                    ->sum('payable_amount') ?? 0.0;
            }
        } catch (\Throwable $e) {
            $totalOrdersAmt = 0.0;
        }

        // Referral global sum (guard moduleExists & class)
        try {
            if (function_exists('moduleExists') && moduleExists('Referral') && class_exists(\Modules\Referral\Entities\Referral::class)) {
                $totalRefCommissions = (float) \Modules\Referral\Entities\Referral::whereNot('referrer_user_id', null)
                    ->where('status', 'approved')
                    ->whereNot('commission_processed_at', null)
                    ->sum('commission_amount') ?? 0.0;
            }
        } catch (\Throwable $e) {
            $totalRefCommissions = 0.0;
        }

        return new self([
            'totalCompletedOrdersAmount' => $totalOrdersAmt,
            'totalReferralCommissions' => $totalRefCommissions,
        ]);
    }
}
