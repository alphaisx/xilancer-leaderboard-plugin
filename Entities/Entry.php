<?php

namespace Modules\Leaderboard\Entities;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $table = 'leaderboard_entries';

    protected $fillable = [
        'user_id',
        'position',
        'metrics_snapshot',
        'score_snapshot',
        'approved_by',
        'approved_at',
        'is_active',
    ];

    protected $casts = [
        'metrics_snapshot' => 'array',
        'score_snapshot' => 'float',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
