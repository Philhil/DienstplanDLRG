<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date', 'hastoauthorize', 'comment',
    ];

    protected $dates = [
        'date',
    ];

    protected $attributes = array(
        'hastoauthorize' => 1,
    );


    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function openpositions()
    {
        return $this->hasMany(Position::class)->whereNull('user_id');
    }

    public function positionwithQualification($qualifiaction_id)
    {
        return $this->positions()->where('qualification_id', $qualifiaction_id);
    }

    public function hasUserPositions($userid)
    {
        return $this->hasMany(Position::class)->where('user_id', '=', $userid)->count() > 0 ? true : false;
    }
}
