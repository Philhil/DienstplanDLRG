<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $fillable = [
        'name', 'short', 'isservicedefault', 'defaultcount', 'defaultrequiredasposition'
    ];
}
