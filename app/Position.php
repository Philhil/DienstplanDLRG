<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
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
}
