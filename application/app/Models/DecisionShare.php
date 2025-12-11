<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DecisionShare extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];

    public function decision()
    {
        return $this->belongsTo(OCRExtraction::class, 'decision_id');
    }

    public function sender()
    {
        return $this->belongsTo(Subscriber::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Subscriber::class, 'receiver_id');
    }
}
