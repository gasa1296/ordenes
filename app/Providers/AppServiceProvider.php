<?php

namespace App\Providers;

use App\Repositories\Interfaces\{
    IInvoiceRepository,
    IProductRepository,
};
use App\Repositories\{
    InvoiceRepository,
    ProductRepository,
};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IProductRepository::class, ProductRepository::class);
        $this->app->bind(IInvoiceRepository::class, InvoiceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
