<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeatureModule extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [];
    
    public $timestamps = false; // Optional if you don’t have created_at/updated_at

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function module()
    {
        return $this->belongsTo(PackageFeatureModule::class, 'module_id');
    }
}
