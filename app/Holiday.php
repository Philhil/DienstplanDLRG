<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';

    protected $fillable = [
        'from', 'to', 'user_id'
    ];

    protected $dates = [
        'create_at',
        'from',
        'to',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
