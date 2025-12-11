<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientFeedback extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_name',
        'client_position',
        'feedback',
        'client_photo',
        'rating',
        'company',
        'website',
        'status',
    ];
}
