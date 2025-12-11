<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OCRExtraction extends Model
{
    use SoftDeletes;

    protected $table = 'ocr_extractions';

    protected $guarded = ['id'];

    protected $fillable = [];

    // protected $dates = ['decided_on'];   

    public function volume()
    {
        return $this->belongsTo(Volume::class);
    }

    /* public function users()
    {
        return $this->belongsToMany(Subscriber::class, 'user_folder_decisions')
            ->withPivot('folder_id')
            ->withTimestamps()
            ->withTrashed();
    }

    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'user_folder_decisions')
            ->withPivot('user_id')
            ->withTimestamps()
            ->withTrashed();
    } */
}
