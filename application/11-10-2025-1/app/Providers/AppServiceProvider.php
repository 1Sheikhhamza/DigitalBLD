<?php

namespace App\Providers;

// All dependency for API
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use App\Session\SubscriberSessionHandler;

// Repository Interface
use App\Repositories\Contracts\CommonRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\PageRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\PackageFeatureModuleRepositoryInterface;
use App\Repositories\Contracts\PackageFeatureRepositoryInterface;
use App\Repositories\Contracts\VolumeRepositoryInterface;
use App\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Contracts\OCRExtractionRepositoryInterface;
use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\InquiryRepositoryInterface;
use App\Repositories\Contracts\VideoRepositoryInterface;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\CareerRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Contracts\ClientFeedbackRepositoryInterface;
use App\Repositories\Contracts\ConfigurationRepositoryInterface;
use App\Repositories\Contracts\DictionaryRepositoryInterface;


// Frontend
use App\Repositories\Contracts\HomeRepositoryInterface;

// Repository
use App\Repositories\Eloquent\CommonRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\PageRepository;
use App\Repositories\Eloquent\PackageRepository;
use App\Repositories\Eloquent\PackageFeatureModuleRepository;
use App\Repositories\Eloquent\PackageFeatureRepository;
use App\Repositories\Eloquent\VolumeRepository;
use App\Repositories\Eloquent\SubscriberRepository;
use App\Repositories\Eloquent\SubscriptionRepository;
use App\Repositories\Eloquent\OCRExtractionRepository;
use App\Repositories\Eloquent\BannerRepository;
use App\Repositories\Eloquent\BlogRepository;
use App\Repositories\Eloquent\TeamRepository;
use App\Repositories\Eloquent\PhotoRepository;
use App\Repositories\Eloquent\VideoRepository;
use App\Repositories\Eloquent\ClientRepository;
use App\Repositories\Eloquent\CareerRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\InquiryRepository;
use App\Repositories\Eloquent\ClientFeedbackRepository;
use App\Repositories\Eloquent\ConfigurationRepository;
use App\Repositories\Eloquent\DictionaryRepository;

// Frontend
use App\Repositories\Eloquent\HomeRepository;
use App\Services\UtilityService;

// View Composer
use App\View\Composers\SubscriptionComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // All dependency for API
        $this->app->bind(CommonRepositoryInterface::class, CommonRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
        $this->app->bind(PackageFeatureModuleRepositoryInterface::class, PackageFeatureModuleRepository::class);
        $this->app->bind(PackageFeatureRepositoryInterface::class, PackageFeatureRepository::class);

        $this->app->bind(VolumeRepositoryInterface::class, VolumeRepository::class);
        $this->app->bind(SubscriberRepositoryInterface::class, SubscriberRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(OCRExtractionRepositoryInterface::class, OCRExtractionRepository::class);
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(CareerRepositoryInterface::class, CareerRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(PhotoRepositoryInterface::class, PhotoRepository::class);
        $this->app->bind(VideoRepositoryInterface::class, VideoRepository::class);
        $this->app->bind(InquiryRepositoryInterface::class, InquiryRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(ClientFeedbackRepositoryInterface::class, ClientFeedbackRepository::class);
        $this->app->bind(ConfigurationRepositoryInterface::class, ConfigurationRepository::class);
        $this->app->bind(DictionaryRepositoryInterface::class, DictionaryRepository::class);

        $this->app->bind(HomeRepositoryInterface::class, HomeRepository::class);
        $this->app->bind(UtilityService::class);

        Session::extend('database', function ($app) {
            $connection = $app['db']->connection(config('session.connection'));
            $table = config('session.table');
            $lifetime = config('session.lifetime');

            return new SubscriberSessionHandler($connection, $table, $lifetime);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('feature', function ($slug) {
            return auth('subscriber')->check() && auth('subscriber')->user()->hasFeature($slug);
        });


        View::composer([
            'auth.subscribers.*',
            // 'another.folder.*',
            // add other folder patterns here
        ], SubscriptionComposer::class);
    }
}
