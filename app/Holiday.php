<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';

    protected $fillable = [
        'from', 'to', 'user_id'
    ];

    protected $casts = [
        'create_at' => 'datetime',
        'from' => 'datetime',
        'to' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
