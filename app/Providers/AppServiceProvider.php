<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Http\Middleware\ProfileAccessMiddleware;
use Illuminate\Support\Facades\Route;

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
    public function boot()
    {
        //$this->registerPolicies();
        //Route::middleware('profile.access', ProfileAccessMiddleware::class);


        // Permitir acceso solo a administradores y superadministradores
        Gate::define('manage-users', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('superadmin');
        });

        // Permitir solo a editores y administradores modificar contenido
        Gate::define('edit-content', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('editor');
        });

        // Solo superadmin puede eliminar usuarios
        Gate::define('delete-users', function (User $user) {
            return $user->hasRole('superadmin');
        });
    }
}