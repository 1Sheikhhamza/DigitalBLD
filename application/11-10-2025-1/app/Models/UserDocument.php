<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDocument extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [];

    protected $guarded = ['id'];

    public function patient(){
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }

    public function files(){
        return $this->hasMany(UserDocumentFile::class, 'document_id', 'id');
    }
}
