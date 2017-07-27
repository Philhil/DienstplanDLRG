<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'email', 'approved', 'role', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isAdmin()
    {
        return $this->role == "admin";
    }

    public function qualifications()
    {
        return $this->belongsToMany(Qualification::class, 'qualification_users')->orderBy('name');
    }

    public function qualificationsNotAssignedToUser()
    {
        $id = $this->id;
        return Qualification::leftJoin('qualification_users',function ($join) use ($id) {
            $join->on('qualifications.id', '=', 'qualification_users.qualification_id');
            $join->on('qualification_users.user_id', '=', DB::raw("'". $id. "'"));
        })->whereNull('qualification_users.qualification_id')->select('qualifications.*')->orderBy('qualifications.name');
    }

    public function hasqualification($qualificationid)
    {
        if($this->belongsToMany(Qualification::class, 'qualification_users')->where('qualification_id', '=', $qualificationid)->count() > 0){
            return true;
        }
        return false;
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
    
    public function authorizedpositions()
    {
        return $this->hasMany(Position::class)->where('isauthorized','=',true)->join('services', 'positions.service_id', '=', 'services.id')
            ->orderBy('services.date')->with('qualification');
    }
}
