<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalDecisionUserNote extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];

    public function decision()
    {
        return $this->belongsTo(OCRExtraction::class, 'decision_id');
    }

    public function user()
    {
        return $this->belongsTo(Subscriber::class, 'user_id');
    }
}
