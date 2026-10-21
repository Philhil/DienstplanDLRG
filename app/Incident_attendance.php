<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Attendance of one helper (member with account or guest by name) at an incident.
 * user_id, station_id and recorded_by have no foreign key: the ids stay set if the
 * referenced user or station is deleted, so the attendance list and statistic stay complete.
 */
class Incident_attendance extends Model
{
    const STATUS_ON_THE_WAY = 'on_the_way';
    const STATUS_PRESENT = 'present';

    const STATUSES = [
        self::STATUS_ON_THE_WAY => 'Auf Anfahrt',
        self::STATUS_PRESENT => 'War da',
    ];

    const SOURCE_SELF = 'self';
    const SOURCE_RECORDED = 'recorded';

    protected $fillable = [
        'incident_id', 'user_id', 'guest_name', 'station_id', 'status', 'reported_at', 'source', 'recorded_by',
        'confirmed_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isPresent()
    {
        return $this->status == self::STATUS_PRESENT;
    }

    public function isConfirmed()
    {
        return !is_null($this->confirmed_at);
    }

    public function isGuest()
    {
        return is_null($this->user_id);
    }

    public function statusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** Name of the helper, also for deleted users (the id stays). */
    public function displayName()
    {
        if (!is_null($this->user_id)) {
            return $this->user ? trim($this->user->first_name . ' ' . $this->user->name) : 'Gelöschter Benutzer (#' . $this->user_id . ')';
        }

        return $this->guest_name;
    }

    /** Name of the station, also for deleted stations (the id stays). */
    public function stationName()
    {
        if (is_null($this->station_id)) {
            return null;
        }

        return $this->station ? $this->station->name : 'Gelöschte Wache (#' . $this->station_id . ')';
    }
}
