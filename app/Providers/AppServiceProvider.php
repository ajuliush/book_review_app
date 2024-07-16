<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Paginator::useBootstrapFive();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use a view composer to bind the status1ReviewCount variable to the sidebar
        View::composer('layouts.sidebar', function ($view) {
            $user = User::with(['reviews' => function ($query) {
                $query->where('status', 1);
            }])->find(Auth::user()->id);

            $status1ReviewCount = $user ? $user->reviews->count() : 0;

            $view->with('status1ReviewCount', $status1ReviewCount);
        });
    }
}
