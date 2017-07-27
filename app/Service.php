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


    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function hasUserPositions($userid)
    {
        return $this->hasMany(Position::class)->where('user_id', '=', $userid)->count() > 0 ? true : false;
    }
}
