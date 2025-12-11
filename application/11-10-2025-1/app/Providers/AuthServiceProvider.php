<?php

namespace App\Providers;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Branch;
use App\Models\Blog;
use App\Models\User;
use App\Policies\DoctorPolicy;
use App\Policies\HospitalPolicy;
use App\Policies\BranchPolicy;
use App\Policies\BlogPolicy;
use App\Policies\UserPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Doctor::class   => DoctorPolicy::class,
        Hospital::class => HospitalPolicy::class,
        Branch::class   => BranchPolicy::class,
        Blog::class     => BlogPolicy::class,
        User::class     => UserPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::enablePasswordGrant();

        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole(['Super Admin', 'super admin'])) {
                return true;
            }
        });

        Passport::ignoreRoutes();

        // Define Passport scopes
        Passport::tokensCan([
            'user' => 'Access user-specific resources',
            'admin' => 'Access admin-specific resources',
        ]);

        // Default scope
        Passport::setDefaultScope([
            'user',
        ]);
    }
}
