<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * An incident (Einsatz), a spontaneous exercise or something else where the attendance of
 * helpers is documented. The attendance list is kept until it is deleted manually.
 */
class Incident extends Model
{
    const TYPE_INCIDENT = 'einsatz';
    const TYPE_EXERCISE = 'uebung';
    const TYPE_OTHER = 'sonstiges';

    const TYPES = [
        self::TYPE_INCIDENT => 'Einsatz',
        self::TYPE_EXERCISE => 'Übung',
        self::TYPE_OTHER => 'Sonstiges',
    ];

    protected $fillable = [
        'client_id', 'type', 'title', 'keyword', 'location', 'dispatch_number', 'start', 'end', 'note',
        'leader_user_id', 'created_by',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function attendances()
    {
        return $this->hasMany(Incident_attendance::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    public function typeLabel()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    // ── State ────────────────────────────────────────────────────────────────

    public function isFinalized()
    {
        return !is_null($this->finalized_at);
    }

    public function isOpen()
    {
        return !$this->isFinalized();
    }

    /** open and not ended yet */
    public function isRunning()
    {
        return $this->isOpen() && is_null($this->end);
    }

    // ── Hours: the duration is the same for every helper of the incident ───────

    /** Duration in minutes (start to end) or null while no end is set. */
    public function durationMinutes()
    {
        if (is_null($this->end) || is_null($this->start)) {
            return null;
        }

        return max(0, (int) $this->start->diffInMinutes($this->end, true));
    }

    /** Number of helpers that were there (status present). */
    public function presentCount()
    {
        return $this->attendances()->where('status', Incident_attendance::STATUS_PRESENT)->count();
    }

    /** Helper minutes = duration x helpers that were there. */
    public function helperMinutes()
    {
        $duration = $this->durationMinutes();

        return is_null($duration) ? null : $duration * $this->presentCount();
    }

    // ── Permissions ──────────────────────────────────────────────────────────

    /** Member of the client of this incident (approved) or admin of it. */
    public function isViewableBy(User $user)
    {
        return $user->isApprovedMemberOfClient($this->client_id) || $user->hasClientRole($this->client_id, 'isAdmin');
    }

    public function isManagedByAdmin(User $user)
    {
        return $user->hasClientRole($this->client_id, 'isAdmin');
    }

    /** Admin or incident editor of the client: may create incidents. */
    public static function canCreate(User $user, $clientId)
    {
        return $user->hasClientRole($clientId, 'isAdmin') || $user->hasClientRole($clientId, 'isIncidentEditor');
    }

    /** Admin, incident editor of the client or leader of this incident. */
    public function canManage(User $user)
    {
        return self::canCreate($user, $this->client_id) || (!is_null($this->leader_user_id) && $this->leader_user_id == $user->id);
    }

    /** Everyone who can manage plus leaders (members with a qualification that may record attendance). */
    public function canRecord(User $user)
    {
        return $this->canManage($user) || $user->canRecordIncidentOfClient($this->client_id);
    }

    /**
     * May the user change or remove this attendance? Managers always, a recording leader only entries
     * that were recorded by himself.
     */
    public function canChangeAttendance(User $user, Incident_attendance $attendance)
    {
        if ($this->canManage($user)) {
            return true;
        }

        return $attendance->source == Incident_attendance::SOURCE_RECORDED
            && $attendance->recorded_by == $user->id
            && $this->canRecord($user);
    }

    /** Every approved member may report himself as long as the incident is open. */
    public function canSelfReport(User $user)
    {
        return $this->isOpen() && $this->isViewableBy($user);
    }
}
