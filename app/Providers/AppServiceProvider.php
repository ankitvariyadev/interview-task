<?php

declare(strict_types=1);

namespace App\Providers;

use App\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Model::unguard();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();

        Gate::before(function ($user): ?bool {
            if ($user->hasRole(Role::Admin->value)) {
                return true;
            }

            return null;
        });
    }
}
