<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageFeature extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = ['name', 'slug', 'description'];

    public function packages()
    {
        return $this->belongsToMany(
            Package::class,
            'package_feature_relations', // pivot table
            'feature_id', // this model key
            'package_id' // related model key
        )->withTimestamps();
    }

    /**
     * A feature can have many modules directly
     */
     public function modules()
    {
        return $this->hasMany(PackageFeatureModule::class, 'feature_id', 'id');
    }
}
