<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Qualification extends Model
{
    protected $fillable = [
        'name', 'short', 'isservicedefault', 'defaultcount', 'defaultrequiredasposition'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'qualification_users');
    }
}
