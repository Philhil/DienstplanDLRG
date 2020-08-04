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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
