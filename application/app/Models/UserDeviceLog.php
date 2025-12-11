<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeviceLog extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(Subscriber::class, 'user_id'); // adjust model name if needed
    }
    
}
