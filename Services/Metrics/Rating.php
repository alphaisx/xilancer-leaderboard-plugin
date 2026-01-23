<?php

namespace Modules\Leaderboard\Services\Metrics;

use Modules\Leaderboard\Services\MetricInterface;
use App\Models\User;

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
        return 1.00;
    }

    public function resolve(User $user): float
    {
        // Must safely handle missing models or modules

        try {
            // Overall Rating Calculation
            $ratings = \App\Models\User::findOrFail($user->id)
                ->freelancer_ratings
                ->where('sender_type', 1) # rating is from client 
                ->avg('rating');

            return (float) $ratings;
        } catch (\Throwable $e) {
            dd($e->getMessage());
            return 0;
        }
    }
}
