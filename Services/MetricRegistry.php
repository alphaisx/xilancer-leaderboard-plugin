<?php

namespace Modules\Rank\Services;

use App\Models\User;

class MetricRegistry
{
    /**
     * @var MetricInterface[]
     */
    protected $metrics = [];

    public function __construct()
    {
        // Register metrics here; add new metric classes under Services/Metrics
        $this->register(new Metrics\CompletedOrders());
        $this->register(new Metrics\TotalReferrals());
        $this->register(new Metrics\Rating());
        // Additional metrics should be registered in this constructor
    }

    public function register(MetricInterface $metric)
    {
        $this->metrics[$metric->key()] = $metric;
    }

    /**
     * @return MetricInterface[]
     */
    public function all(): array
    {
        return $this->metrics;
    }

    /**
     * Resolve all metrics for a user using a prebuilt MetricContext.
     *
     * Calls each metric->resolve exactly once and computes 'total' from that single value.
     *
     * @param User $user
     * @param MetricContext|null $context
     * @return array
     */
    public function resolveAllFor(User $user, ?MetricContext $context = null): array
    {
        $out = [];
        foreach ($this->metrics as $m) {
            try {
                $value = (float) $m->resolve($user, $context);
                $value = round($value, 4);
                $out[$m->key()] = [
                    'label' => $m->label(),
                    'weight' => $m->weight(),
                    'value' => $value,
                    'total' => $m->weight() * $value,
                ];
            } catch (\Throwable $e) {
                $out[$m->key()] = [
                    'label' => $m->label(),
                    'weight' => $m->weight(),
                    'value' => 0,
                    'total' => $m->weight() * 0,
                ];
            }
        }
        return $out;
    }

    public function computeScore(array $metricData): float
    {
        $score = 0.0;
        foreach ($metricData as $m) {
            $score += ($m['value'] * ($m['weight'] ?? 1));
        }
        return (float) $score;
    }
}
