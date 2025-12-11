<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $guarded = ['id'];

    protected $fillable = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function devices()
    {
        return $this->hasMany(UserDeviceLog::class, 'user_id');
    }


    // Relationships
    public function folders()
    {
        return $this->hasMany(Folder::class, 'user_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'user_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'subscriber_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->where('expire_date', '>=', now()->toDateString())
            ->latest('subscription_date')
            ->with('package.features.modules'); // eager load features & modules
    }

    /**
     * Get the active package model
     */
    public function activePackage()
    {
        return $this->activeSubscription?->package;
    }

    /**
     * Get all features of the active package
     */
    public function features()
    {
        return $this->activePackage()?->features ?? collect();
    }

    /**
     * Get all modules of the active package
     */
    public function modules()
    {
        return $this->features()->flatMap(fn($feature) => $feature->modules) ?? collect();
    }

    /**
     * Check if subscriber has a feature by slug
     */
    public function hasFeature(string $slug): bool
    {
        return $this->features()->contains(fn($feature) => $feature->slug === $slug);
    }

    /**
     * Check if subscriber has a module by route name
     */
    public function hasModule(string $routeName): bool
    {
        return $this->modules()->contains(fn($module) => $module->route_name === $routeName);
    }

    /**
     * Helper for blade: check both feature slug or module route
     */
    public function canAccessModule(string $slugOrRoute): bool
    {
        return $this->hasFeature($slugOrRoute) || $this->hasModule($slugOrRoute);
    }

    /**
     * ✅ Centralized resource-based permission check
     * e.g. hasAnyPermissionOnResource('folder') → checks all modules with route containing 'folder'
     */
    public function hasAnyPermissionOnResource(string $slugOrRoute): bool
    {
        return
            $this->hasFeature($slugOrRoute) ||
            $this->modules()->contains(function ($module) use ($slugOrRoute) {
                return $module->route_name === $slugOrRoute ||
                    ($module->feature && $module->feature->slug === $slugOrRoute);
            });
    }
}
