<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\OrderRepository;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

use App\Repositories\RateChartRepository;
use App\Repositories\Interfaces\RateChartRepositoryInterface;

class OrderServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        // $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RateChartRepositoryInterface::class, RateChartRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
