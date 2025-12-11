<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'target_type',
        'user_id',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(Subscriber::class, 'user_id');
    }
}
