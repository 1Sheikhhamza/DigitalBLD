<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->orderBy('created_at', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(BlogLike::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->where('is_like', true)->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->likes()->where('is_like', false)->count();
    }

    public function isLikedBy($subscriberId)
    {
        return $this->likes()
            ->where('subscriber_id', $subscriberId)
            ->where('is_like', true)
            ->exists();
    }

    public function isDislikedBy($subscriberId)
    {
        return $this->likes()
            ->where('subscriber_id', $subscriberId)
            ->where('is_like', false)
            ->exists();
    }
}
