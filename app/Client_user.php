<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client_user extends Model
{
    protected $table = 'client_user';

    protected $fillable = [
        'client_id',
        'user_id',
        'isAdmin',
        'isIncidentEditor',
        'home_station_id'
    ];

    protected $casts = [
        'create_at' => 'datetime'
    ];
}
