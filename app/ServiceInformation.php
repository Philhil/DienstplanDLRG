<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceInformation extends Model
{
    protected $table = 'service_information';

    use HasFactory;

    protected $fillable = [
        'content', 'service_id', 'user_id', 'client_id'
    ];

    protected $dates = [
        'create_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
