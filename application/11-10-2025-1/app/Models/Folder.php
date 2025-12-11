<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];

    public function files()
    {
        return $this->hasMany(FolderFile::class);
    }

    /* public function users()
    {
        return $this->belongsToMany(Subscriber::class, 'user_folder_decisions')
            ->withPivot('decision_id')
            ->withTimestamps()
            ->withTrashed();
    }

    public function decisions()
    {
        return $this->belongsToMany(OCRExtraction::class, 'user_folder_decisions')
            ->withPivot('user_id')
            ->withTimestamps()
            ->withTrashed();
    } */


    // In Folder.php (or User.php depending on context)
    public function userFolderDecisions()
    {
        return $this->hasMany(UserFolderDecision::class, 'folder_id');
    }
}
