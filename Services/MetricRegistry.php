<?php

namespace Modules\Leaderboard\Services;

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

    public function resolveAllFor(User $user): array
    {
        $out = [];
        foreach ($this->metrics as $m) {
            try {
                $out[$m->key()] = [
                    'label' => $m->label(),
                    'weight' => $m->weight(),
                    'value' => round((float) $m->resolve($user), 4),
                ];
            } catch (\Throwable $e) {
                $out[$m->key()] = [
                    'label' => $m->label(),
                    'weight' => $m->weight(),
                    'value' => 0,
                ];
            }
        }
        return $out;
    }

    public function computeScore(array $metricData): float
    {
        $score = 0.0;
        foreach ($metricData as $m) {
            $score += $m['value'];
            // $score += ($m['value'] * ($m['weight'] ?? 1));
        }
        return (float) $score;
    }
}
