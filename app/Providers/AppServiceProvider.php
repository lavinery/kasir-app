<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use App\Models\Produk;
use App\Observers\ProductStockObserver;
use App\Observers\ProdukStockObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        view()->composer('layouts.master', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('layouts.auth', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('auth.login', function ($view) {
            $view->with('setting', Setting::first());
        });
        $this->app->singleton(\App\Services\BarangHabisService::class);
        $this->app->singleton(\App\Services\MemberStatsService::class, function ($app) {
            return new \App\Services\MemberStatsService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!file_exists(public_path('storage'))) {
            $target = storage_path('app/public');
            $link = public_path('storage');
            if (!file_exists($target)) {
                mkdir($target, 0755, true);
            }
            if (mkdir($link, 0755, true)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($target, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($iterator as $item) {
                    if ($item->isDir()) {
                        mkdir($link . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), 0755, true);
                    } else {
                        copy($item, $link . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                    }
                }
            }
        }
        Produk::observe(ProductStockObserver::class);
        Produk::observe(ProdukStockObserver::class);
    }
}
