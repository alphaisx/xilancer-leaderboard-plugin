<?php

namespace Modules\Rank\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Rank\Services\MetricRegistry;
use Modules\Rank\Services\MetricContext;
use Modules\Rank\Entities\Candidate;
use Modules\Rank\Entities\Entry;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    protected $registry;

    public function __construct(MetricRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function admin_index()
    {
        $candidates = Candidate::orderByDesc('score')->limit(50)->get();
        return view('rank::admin.leaderboard.manage-leaderboard', compact('candidates'));
    }

    public function user_index()
    {
        $candidates = Entry::where('is_active', true)->orderBy('position')->limit(20)->with('user')->get();
        return view('rank::frontend.leaderboard.leaderboard-view', compact('candidates'));
    }

    public function generateCandidates(Request $request)
    {
        $number_of_candidates = 18; // fixed number for now

        try {
            // Build MetricContext ONCE per run (precompute global aggregates)
            $context = MetricContext::build();

            $topCandidates = []; // fixed-size buffer of top candidates

            // Helper: replace min candidate if needed
            $maybeInsertTop = function (array $candidate) use (&$topCandidates, $number_of_candidates) {
                if (empty($topCandidates)) {
                    $topCandidates[] = $candidate;
                    return;
                }
                if (count($topCandidates) < $number_of_candidates) {
                    $topCandidates[] = $candidate;
                    return;
                }
                // find index of smallest score
                $minIdx = 0;
                $minScore = $topCandidates[0]['score'];
                for ($i = 1, $len = count($topCandidates); $i < $len; $i++) {
                    if ($topCandidates[$i]['score'] < $minScore) {
                        $minScore = $topCandidates[$i]['score'];
                        $minIdx = $i;
                    }
                }
                if ($candidate['score'] > $minScore) {
                    $topCandidates[$minIdx] = $candidate;
                }
            };

            // Stream users in chunks to scale to 100k+ users
            User::where(function ($q) {
                $q->where('user_type', 2);
            })->chunkById(1000, function ($users) use (&$topCandidates, $context, $maybeInsertTop) {
                foreach ($users as $user) {
                    try {
                        $metrics = $this->registry->resolveAllFor($user, $context);
                        $score = $this->registry->computeScore($metrics);

                        $candidate = [
                            'user_id' => $user->id,
                            'metrics' => json_encode($metrics),
                            'score' => $score,
                            'computed_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $maybeInsertTop($candidate);
                    } catch (\Throwable $e) {
                        // skip user on failure; avoid halting the entire run
                        continue;
                    }
                }
            });

            // Persist top candidates (sorted desc by score)
            $top = collect($topCandidates)->sortByDesc('score')->take($number_of_candidates)->values()->all();

            DB::transaction(function () use ($top) {
                \Modules\Rank\Entities\Candidate::truncate();
                if (!empty($top)) {
                    \Modules\Rank\Entities\Candidate::insert($top);
                }
            });
        } catch (\Throwable $th) {
            // swallow or log as appropriate
        }

        return redirect()->route('admin.leaderboard.all')->with('status', 'Leaderboard candidates generated successfully.');
    }

    public function approve(Request $request)
    {
        $request->validate([
            'user_id' => 'integer|exists:users,id',
            'position' => 'required|integer|min:1|max:20'
        ]);
        $userId = $request->user_id;
        $position = (int) $request->input('position');

        $candidate = Candidate::where('user_id', $userId)->firstOrFail();

        DB::transaction(function () use ($candidate, $position) {
            // deactivate any active entry at this position
            Entry::where('position', $position)->delete();

            Entry::updateOrCreate(
                ['user_id' => $candidate->user_id],
                [
                    'position' => $position,
                    'metrics_snapshot' => $candidate->metrics,
                    'score_snapshot' => $candidate->score,
                    'approved_by' => Auth::user()->id,
                    'approved_at' => now(),
                    'is_active' => true,
                ]
            );
            // Send notification to user
            freelancer_notification($candidate->user_id, $candidate->user_id, 'Leaderboard', __('Congratulations on being featured on the leaderboard, a closer step to your goals. Keep the energy up and going!',));
        });

        return back()->with(toastr_success(__('Approved and published successfully.')));
    }

    public function remove(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:leaderboard_entries,user_id',
        ]);
        $userId = (int) $request->input('user_id');

        $entry = Entry::where('user_id', $userId)->firstOrFail();

        DB::transaction(function () use ($entry) {
            // deactivate any active entry at this position
            $entry->delete();
        });

        return back()->with(toastr_success(__('Leaderboard entry removed successfully.')));
    }

    public function bulk_actions(Request $request)
    {
        $request->validate([
            'payloads' => 'required|array',
            'payloads.*.id' => 'required|integer|exists:users,id',
            'payloads.*.position' => 'required|integer|min:1|max:20',
            'action' => 'required|string|in:remove,approve_rank',
        ], [
            'payloads.*.id.exists' => __('The selected user does not exist.'),
            'payloads.*.position.min' => __('Position must be at least 1.'),
            'payloads.*.position.max' => __('Position may not be greater than 20.'),
            'action.in' => __('Invalid action specified. Only "remove" and "approve_rank" are allowed.'),
        ]);
        $action = $request->input('action');
        $payloads = $request->input('payloads');

        try {
            return DB::transaction(function () use ($payloads, $action) {
                foreach ($payloads as $payload) {
                    $userId = $payload['id'];
                    $position = (int) $payload['position'];

                    if ($action === 'approve_rank') {
                        $candidate = Candidate::where('user_id', $userId)->firstOrFail();

                        // deactivate any active entry at this position
                        Entry::where('position', $position)->delete();

                        Entry::updateOrCreate(
                            ['user_id' => $candidate->user_id],
                            [
                                'position' => $position,
                                'metrics_snapshot' => $candidate->metrics,
                                'score_snapshot' => $candidate->score,
                                'approved_by' => Auth::user()->id,
                                'approved_at' => now(),
                                'is_active' => true,
                            ]
                        );
                        // Send notification to user
                        freelancer_notification($candidate->user_id, $candidate->user_id, 'Leaderboard', __('Congratulations on being featured on the leaderboard, a closer step to your goals. Keep the energy up and going!',));
                    } elseif ($action === 'remove') {
                        try {
                            $entry = Entry::where('user_id', $userId)->firstOrFail();
                            $entry->delete();
                        } catch (\Throwable $e) {
                            // Entry not found, skip
                            continue;
                        }
                    }
                }
                return response()->json([
                    'status' => 'success',
                    'message' => __('Bulk action completed successfully.'),
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => __('An error occurred while processing bulk actions. :error', ['error' => $th->getMessage()]),
            ], 500);
        }
    }
}
