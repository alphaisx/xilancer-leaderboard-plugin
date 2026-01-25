<?php

namespace Modules\Rank\Services;

use App\Models\User;

interface MetricInterface
{
    public function key(): string;
    public function label(): string;
    public function weight(): float;

    /**
     * Resolve metric value for a single user.
     * Accepts an optional MetricContext which contains precomputed global aggregates.
     *
     * Must be pure (no side effects) and must NOT run global aggregates.
     *
     * @param User $user
     * @param MetricContext|null $context
     * @return float
     */
    public function resolve(User $user, ?MetricContext $context = null): float;
}
