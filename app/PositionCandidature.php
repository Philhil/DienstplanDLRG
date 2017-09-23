<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionCandidature extends Model
{
    protected $table = 'positioncandidatures';

    protected $fillable = [
        'user_id', 'position_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
