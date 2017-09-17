<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{

    protected $fillable = [
        'qualification_id', 'service_id'
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

    public function candidatures()
    {
        return $this->hasMany(PositionCandidature::class);
    }

    public function candidaturesOfUser($user_id)
    {
        return $this->candidatures()->where('user_id', $user_id);
    }
}
