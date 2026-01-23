<div class="leaderboard-top3 d-flex gap-3">
    @foreach ($entries as $entry)
        <a href="{{ url('users/' . $entry->user_id) }}" class="card leaderboard-hero p-3 text-decoration-none">
            <div class="card-body text-center">
                <h5 class="mb-1">{{ optional($entry->user)->name ?? 'User #' . $entry->user_id }}</h5>
                <div class="position">{{ $entry->position }}</div>
                <div class="score">{{ number_format($entry->score_snapshot, 2) }}</div>
                <div class="small-metrics d-none d-md-block mt-2">
                    @foreach ($entry->metrics_snapshot as $m)
                        <div>{{ $m['label'] ?? '' }}: {{ $m['value'] ?? 0 }}</div>
                    @endforeach
                </div>
            </div>
        </a>
    @endforeach
</div>
