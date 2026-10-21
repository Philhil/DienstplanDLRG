<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * A station (Wache) of a client. Incident attendances reference stations by id only, a deleted
 * station keeps its id in the attendance list (see Incident_attendance::stationName()).
 */
class Station extends Model
{
    protected $fillable = [
        'client_id', 'name',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
