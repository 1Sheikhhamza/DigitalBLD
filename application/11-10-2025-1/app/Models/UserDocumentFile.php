<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDocumentFile extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [];

    protected $guarded = ['id'];

    public function getFilePathAttribute($value)
    {
        $baseUrl = rtrim(env('BACKEND_URL'), '/');
        return $baseUrl . '/' . ltrim($value, '/');
    }

    public function document(){
        return $this->belongsTo(UserDocument::class, 'document_id', 'id');
    }
}
