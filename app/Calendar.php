<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $fillable = [
      'title', 'client_id', 'verantwortlicher', 'date', 'dateEnd', 'location'
    ];

    protected $casts = [
        'date' => 'datetime',
        'dateEnd' => 'datetime'
    ];

    public function  client()
    {
        return $this->belongsTo(Client::class);
    }

}
