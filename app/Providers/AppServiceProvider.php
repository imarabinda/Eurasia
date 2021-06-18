<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Fabric;
use App\Observers\FabricObserver;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Models\File;
use App\Observers\FileObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(125);
        Product::observe(ProductObserver::class);
        Fabric::observe(FabricObserver::class);
        File::observe(FileObserver::class);
    }
}
