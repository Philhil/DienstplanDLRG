<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'seasonStart',
        'isMailinglistCommunication',
        'weeklyServiceviewEmail',
        'mailinglistAddress',
        'mailSenderName',
        'mailReplyAddress',
        'defaultServiceStart',
        'defaultServiceEnd'
    ];

    protected $dates = [
        'create_at', 'seasonStart'
    ];

    public function user_all()
    {
        return $this->belongsToMany(User::class, 'client_user')->orderBy('name');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'client_user')
            ->where('client_user.approved', '=', true)->orderBy('name');
    }

    public function client_authuser()
    {
        return $this->hasMany( Client_user::class)
            ->where('user_id', '=', Auth::user()->id);
    }

    public function noAdmins()
    {
        return $this->belongsToMany(User::class, 'client_user')
            ->where('client_user.approved', '=', true)
            ->where('isAdmin', '=', false)->orderBy('name');
    }

    public function Admins()
    {
        return $this->belongsToMany(User::class, 'client_user')
            ->where('client_user.approved', '=', true)
            ->where('isAdmin', '=', true)->orderBy('name');
    }

    public function noTrainingEditors()
    {
        return $this->belongsToMany(User::class, 'client_user')
            ->where('client_user.approved', '=', true)
            ->where('isTrainingEditor', '=', false)->orderBy('name');
    }

    public function TrainingEditors()
    {
        return $this->belongsToMany(User::class, 'client_user')
            ->where('client_user.approved', '=', true)
            ->where('isTrainingEditor', '=', true)->orderBy('name');
    }

    public function Qualifications()
    {
        return $this->hasMany( Qualification::class);
    }

    public function Services()
    {
        return $this->hasMany( Service::class);
    }

    public function Season()
    {
        $season = $this->seasonStart;
        $season->year(Carbon::now()->format('Y'));

        if ($season->isFuture())
        {
            $from = $season->copy()->subYears(1);
            $to = $season;
        }
        else
        {
            $from = $season;
            $to = $season->copy()->addYear();
        }
        return ["from" => $from, "to" => $to];
    }
}
