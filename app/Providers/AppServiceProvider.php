<?php

namespace App\Providers;

use App\Enums\Roles;
use App\Models\Task;
use App\Policies\TaskPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{



    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }



    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole(Roles::ADMIN->value) ? true : null;
        });

        Gate::policy(Task::class,TaskPolicy::class);
    }
}
