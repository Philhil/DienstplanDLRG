<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $fillable = [
      'titel', 'client_id', 'verantwortlicher', 'date', 'dateEnd', 'location', 'sendbydatetime',
    ];

    protected $dates = [
       'date', 'dateEnd', 'sendbytime'
    ];

    public function  client()
    {
        return $this->belongsTo((Client::class));
    }

}
