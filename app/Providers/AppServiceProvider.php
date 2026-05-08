<?php

namespace App\Providers;

use App\Repositories\Eloquent\EloquentTaskRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Test\TestTaskRespository;
use App\Repositories\Test\TestUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TaskRepositoryInterface::class,
            EloquentTaskRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        // FIXME Last Bind Wins remove them in production

        // $this->app->bind(
        //     UserRepositoryInterface::class,
        //     TestUserRepository::class
        // );

        // $this->app->bind(
        //     TaskRepositoryInterface::class,
        //     TestTaskRespository::class
        // );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
