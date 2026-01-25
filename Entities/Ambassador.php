<?php

namespace Modules\Rank\Entities;

use Illuminate\Database\Eloquent\Model;

class Ambassador extends Model
{
    protected $table = 'leaderboard_ambassadors';

    protected $fillable = [
        'user_id',
        'notes',
        'is_ambassador',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'notes' => 'string',
        'is_ambassador' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
