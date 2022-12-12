<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    protected $table = 'newss';

    use HasFactory;

    protected $fillable = [
        'title', 'content',
    ];

    protected $dates = [
        'create_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
