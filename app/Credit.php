<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'position_id', 'qualification_id', 'points'
    ];

    protected $dates = [
        'create_at', 'updated_at'
    ];

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
