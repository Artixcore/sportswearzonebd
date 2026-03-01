<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('partials.store-nav', function ($view) {
            $view->with('cartCount', (int) array_sum(session('cart', [])));
        });

        \Illuminate\Support\Facades\View::composer('partials.app-nav', function ($view) {
            $view->with('cartCount', (int) array_sum(session('cart', [])))
                ->with('categories', Category::whereNull('parent_id')->orderBy('sort_order')->get());
        });
    }
}
