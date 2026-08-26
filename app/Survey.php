<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $table = 'surveys';

    protected $fillable = [
        'client_id',
        'title',
        'content',
        'dateStart',
        'dateEnd',
        'mandatory',
        'passwordConfirmationRequired',
        'qualification_id'
    ];

    protected $casts = [
        'dateStart' => 'datetime',
        'dateEnd' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            Survey_user::class,
            'survey_id', // survey key on Survey_user table...
            'id', // Foreign key on Users table...
            'id', // Local key on survey table...
            'user_id' // user key on Survey_user table...
        );
    }

    public function survey_user()
    {
        return $this->hasMany(Survey_user::class);
    }

}
