<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{

    protected $fillable = [
        'qualification_id', 'service_id', 'requiredposition'
    ];

    protected $casts = [
        'create_at' => 'datetime',
        'updated_at' => 'datetime'
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

    public function candidatures()
    {
        return $this->hasMany(PositionCandidature::class);
    }

    public function candidaturesOfUser($user_id)
    {
        return $this->candidatures()->where('user_id', $user_id);
    }

    public function getCredit()
    {
        return $this->hasOne(Credit::class);
    }

    public function getClientId()
    {
        $client = -1;
        if ($this->service()->exists())
        {
            $client = $this->service->client_id;
        }
        elseif ($this->training()->exists())
        {
            $client = $this->training->client_id;
        }
        return $client;
    }
}
