<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\StudentRepository;
use App\Repositories\Interfaces\StudentRepositoryInterface;

class StudentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        // $this->app->bind('StudentRepository', function ($app) {
        //     return new StudentRepository();
        // });

        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
