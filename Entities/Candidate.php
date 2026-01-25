<?php

namespace Modules\Rank\Entities;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'leaderboard_candidates';

    protected $fillable = [
        'user_id',
        'metrics',
        'score',
        'computed_at',
    ];

    protected $casts = [
        'metrics' => 'json',
        'score' => 'float',
        'computed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function entry()
    {
        return $this->hasOne(Entry::class, 'user_id', 'user_id')->where('is_active', true);
    }
}
