<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
