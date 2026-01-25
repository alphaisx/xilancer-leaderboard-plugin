<?php

namespace Modules\Rank\Services\Metrics;

use Modules\Rank\Services\MetricInterface;
use Modules\Rank\Services\MetricContext;
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

    /**
     * Resolve must NOT run global aggregates.
     * Uses MetricContext->totalCompletedOrdersAmount for global totals.
     */
    public function resolve(User $user, ?MetricContext $context = null): float
    {
        try {
            if (!class_exists(\App\Models\Order::class)) {
                return 0;
            }

            $total_commissions = $context?->totalCompletedOrdersAmount ?? 0.0;

            // User Calculation: only per-user queries
            $userOrdersQuery = \App\Models\Order::where('freelancer_id', $user->id)->where('status', 3);
            $total_users_commission = (float) $userOrdersQuery->where('payment_status', 'complete')->sum('payable_amount') ?? 0.0;
            $orders_count = (int) $userOrdersQuery->where('payment_status', 'complete')->count();

            $percent = 0.0;
            if ($total_commissions > 0.0) {
                $percent = ($total_users_commission / $total_commissions) * 100.0;
            }

            return (float) $orders_count + $percent;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
