<?php

namespace Modules\Leaderboard\Services;

use App\Models\User;

interface MetricInterface
{
    public function key(): string;
    public function label(): string;
    public function weight(): float;
    public function resolve(User $user): float;
}
