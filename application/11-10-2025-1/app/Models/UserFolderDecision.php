<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFolderDecision extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(Subscriber::class, 'user_id');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function decision()
    {
        return $this->belongsTo(OCRExtraction::class, 'decision_id');
    }
}
