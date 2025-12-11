<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];
    
    protected $dates = ['subscription_date', 'expire_date'];
    
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function features()
    {
        // Get features via the package
        return $this->package ? $this->package->features : collect();
    }

    public function modules()
    {
        // Get modules via the package
        return $this->package ? $this->package->features->flatMap(fn($f) => $f->modules) : collect();
    }
}
