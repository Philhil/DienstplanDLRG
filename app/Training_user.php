<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training_user extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'training_id', 'user_id', 'position_id', 'user_comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
