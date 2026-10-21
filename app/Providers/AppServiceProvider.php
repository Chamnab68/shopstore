<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
// បន្ថែម Namespace របស់ Service ទាំងពីរ
use App\Services\TelegramService;
use App\Services\NotificationService;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ចុះឈ្មោះ TelegramService ជា Singleton (បង្កើតតែម្តងប្រើបានគ្រប់កន្លែង)
        $this->app->singleton(TelegramService::class, function ($app) {
            return new TelegramService();
        });

        // ចុះឈ្មោះ NotificationService និងបញ្ចូល TelegramService ទៅក្នុង constructor របស់វា
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService($app->make(TelegramService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // កំណត់ប្រវែង String សម្រាប់ Database Migration
        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);
    }
}