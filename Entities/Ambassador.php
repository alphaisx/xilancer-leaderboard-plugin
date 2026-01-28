<?php

namespace Modules\Rank\Entities;

use Illuminate\Database\Eloquent\Model;

class Ambassador extends Model
{
    protected $table = 'ambassadors';

    protected $fillable = [
        'user_id',
        'fullname',
        'email',
        'phone',
        'address',
        'school',
        'level',
        'course',
        'reason',
        'is_ambassador',
        'notes',
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
