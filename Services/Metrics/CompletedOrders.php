<?php

namespace Modules\Leaderboard\Services\Metrics;

use Modules\Leaderboard\Services\MetricInterface;
use App\Models\User;

class CompletedOrders implements MetricInterface
{
    public function key(): string
    {
        return 'completed_orders';
    }
    public function label(): string
    {
        return 'Completed Orders';
    }
    public function weight(): float
    {
        return 1.5;
    }

    public function resolve(User $user): float
    {
        // Must safely handle missing models or modules

        try {
            if (!class_exists(\App\Models\Order::class)) {
                return 0;
            }
            // Overall Table Calculation
            $total_commissions = \App\Models\Order::where('status', 3)->where('payment_status', 'complete')->sum('payable_amount') ?? 0;

            // User Calculation
            $total_orders = \App\Models\Order::where('freelancer_id', $user->id)->where('status', 3); # 3=complete
            $total_users_commissions = $total_orders->where('payment_status', 'complete')->sum('payable_amount') ?? 0;
            $percent = (float) ($total_users_commissions / $total_commissions) * 100;
            // dd($this->label(), $total_commissions, $total_users_commissions, $percent, $total_orders->count());
            return (float) $total_orders->count() + $percent;
        } catch (\Throwable $e) {
            dd($e->getMessage());
            return 0;
        }
    }
}
