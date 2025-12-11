<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [];

    public function features()
    {
        return $this->belongsToMany(
            PackageFeature::class,
            'package_feature_relations', // pivot table
            'package_id',               // foreign key on pivot table for Package
            'feature_id'                // foreign key on pivot table for Feature
        )->withTimestamps();
    }

    /**
     * Get all modules of this package (not a relationship)
     */
    public function getModulesAttribute()
    {
        // Make sure features are loaded
        $this->loadMissing('features.modules');

        return $this->features->flatMap(function ($feature) {
            return $feature->modules;
        });
    }


    /**
     * A package has many subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }
}
