@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Leaderboard</h1>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
            @foreach ($entries as $entry)
                <div class="col">
                    <a href="{{ url('users/' . $entry->user_id) }}" class="card h-100 text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="mb-1">{{ optional($entry->user)->name ?? 'User #' . $entry->user_id }}</h5>
                                    <div class="small text-muted">Position #{{ $entry->position }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="h4 mb-0">{{ number_format($entry->score_snapshot, 1) }}</div>
                                </div>
                            </div>
                            <div class="mt-2 metrics-preview">
                                @foreach ($entry->metrics_snapshot as $m)
                                    <div class="metric-item">{{ $m['label'] ?? '' }}: <span
                                            class="fw-bold">{{ $m['value'] ?? 0 }}</span></div>
                                @endforeach
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
