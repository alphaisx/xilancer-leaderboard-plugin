<?php

namespace Modules\Rank\Services\Metrics;

use Modules\Rank\Services\MetricInterface;
use Modules\Rank\Services\MetricContext;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Rating implements MetricInterface
{
    public function key(): string
    {
        return 'rating';
    }
    public function label(): string
    {
        return 'Rating';
    }
    public function weight(): float
    {
        return 3.00;
    }

    /**
     * Rating does not need global aggregates but accepts context for signature compatibility.
     *
     * Defensive: never throw, log missing dependencies, return 0 when unavailable.
     */
    public function resolve(User $user, ?MetricContext $context = null): float
    {
        try {
            // Basic guard
            if (!$user || !isset($user->id)) {
                Log::warning('[Leaderboard][Rating] Invalid user provided to Rating::resolve');
                return 0.0;
            }

            // Prefer relation query if relation method exists (avoids guessing Rating model schema)
            if (method_exists($user, 'freelancer_ratings')) {
                try {
                    $avg = $user->freelancer_ratings()->where('sender_type', 1)->avg('rating');
                    return (float) ($avg ?: 0.0);
                } catch (\Throwable $e) {
                    Log::warning('[Leaderboard][Rating] Error querying freelancer_ratings relation for user_id=' . $user->id . ' — ' . $e->getMessage());
                    return 0.0;
                }
            }

            // Fallback: attempt direct Rating model query if it exists
            if (class_exists(\App\Models\Rating::class)) {
                try {
                    $avg = \App\Models\Rating::where('freelancer_id', $user->id)->where('sender_type', 1)->avg('rating');
                    return (float) ($avg ?: 0.0);
                } catch (\Throwable $e) {
                    Log::warning('[Leaderboard][Rating] Error querying App\Models\Rating for user_id=' . $user->id . ' — ' . $e->getMessage());
                    return 0.0;
                }
            }

            // Nothing available: log and return 0
            Log::warning('[Leaderboard][Rating] No rating relation or model available. Returning 0 for user_id=' . $user->id);
            return 0.0;
        } catch (\Throwable $e) {
            // Final safety: ensure metric never throws
            Log::warning('[Leaderboard][Rating] Unexpected error for user_id=' . ($user->id ?? 'n/a') . ' — ' . $e->getMessage());
            return 0.0;
        }
    }
}
