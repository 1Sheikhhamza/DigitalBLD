<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageFeatureRelation extends Model
{

    protected $guarded = ['id'];

    protected $fillable = [];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function feature()
    {
        return $this->belongsTo(PackageFeature::class, 'feature_id');
    }
}
