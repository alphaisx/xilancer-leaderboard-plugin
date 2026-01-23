<?php

namespace Modules\Leaderboard\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Leaderboard\Services\MetricRegistry;
use Modules\Leaderboard\Entities\Candidate;
use Modules\Leaderboard\Entities\Entry;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    protected $registry;

    public function __construct(MetricRegistry $registry)
    {
        $this->middleware('auth');
        // $this->middleware('can:manage-leaderboard');
        $this->registry = $registry;
    }

    public function index()
    {
        $candidates = Candidate::orderByDesc('score')->limit(50)->get();
        return view('leaderboard::admin.index', compact('candidates'));
    }

    public function generateCandidates(Request $request)
    {
        try {
            // Collect freelancer users — platform-specific: fallback to users with is_freelancer flag or role
            $users = User::where(function ($q) {
                $q->where('user_type', 2);
            })->get();
            // compute metrics
            $payload = [];
            foreach ($users as $user) {
                $metrics = $this->registry->resolveAllFor($user);
                $score = $this->registry->computeScore($metrics);
                $payload[] = [
                    'user_id' => $user->id,
                    'metrics' => json_encode($metrics),
                    'score' => $score,
                    'computed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // dd(collect($payload)->where('score', '>', 15)->sortByDesc('score')->take(20));
            // store top 20
            DB::transaction(function () use ($payload) {
                \Modules\Leaderboard\Entities\Candidate::truncate();
                $top = collect($payload)->where('score', '>', 15)->sortByDesc('score')->take(20)->values()->all();

                // Using the Model allows the $casts defined above to work
                \Modules\Leaderboard\Entities\Candidate::insert($top);
            });
        } catch (\Throwable $th) {
            // return 
        }
        return redirect()->route('admin.leaderboard.all')->with('status', 'Candidates generated');
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
            Entry::where('position', $position)->where('is_active', true)->update(['is_active' => false]);

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
        });

        return back()->with(toastr_success(__('Approved and published successfully')));
    }
}
