<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'title', 'client_id', 'content', 'date', 'dateEnd', 'location', 'sendbydatetime',
    ];

    protected $dates = [
        'date', 'dateEnd', 'sendbydatetime'
    ];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function openpositions()
    {
        return $this->hasMany(Position::class)->whereNull('user_id');
    }

    public function openpositions_required()
    {
        return $this->hasMany(Position::class)->whereNull('user_id')
            ->where('requiredposition', '=', 1);
    }

    public function positionwithQualification($qualifiaction_id)
    {
        return $this->positions()->where('qualification_id', $qualifiaction_id);
    }

    public function hasUserPositions($userid)
    {
        return $this->hasMany(Training_user::class, 'training_id', 'id')->where('user_id', '=', $userid)->count() > 0 ? true : false;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function training_users()
    {
        return $this->hasMany(Training_user::class, 'training_id', 'id');
    }
}
