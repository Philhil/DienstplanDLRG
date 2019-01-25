<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'seasonStart',
        'isMailinglistCommunication',
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
}
