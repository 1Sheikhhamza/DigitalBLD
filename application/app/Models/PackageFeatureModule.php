<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageFeatureModule extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];


    // Belongs to a feature
    public function feature()
    {
        return $this->belongsTo(PackageFeature::class, 'feature_id');
    }
}
