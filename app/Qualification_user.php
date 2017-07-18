<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qualification_user extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'qualification_id', 'user_id',
    ];
}
