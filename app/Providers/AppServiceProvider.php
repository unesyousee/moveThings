<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\HeavyThing;
use App\Observers\OrderObserver;
use App\Thirdparty;
use Carbon\Carbon;
use App\Price;
use App\Role;
use App\User;
use App\Order;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        view()->composer('admin.layout.master', function ($view) {
            $heavy = HeavyThing::all();
            $view->with('heavy', $heavy);
        });
        view()->composer('admin.layout.aside', function ($view) {

            $third = Thirdparty::all();
//            dd($third->user);
            $view->with('third', $third);
        });
        view()->composer('admin.layout.master', function ($view) {
            $prices = Price::all();
            $view->with('prices', $prices);
        });
        view()->composer('admin.layout.master', function ($view) {
            $roles = Role::all();
            $view->with('roles', $roles);
        });
        view()->composer('admin.layout.header', function ($view) {
            $n_seen = Order::where('seen', 0)->where(function ($query) {
                $query->where('status', "!", 7);
            })->orderBy('created_at', 'DESC')->get();
            $view->with('n_seen', $n_seen);
        });
        view()->composer('admin.layout.header', function ($view) {
            $todayOrders = Order::whereDate('created_at', Carbon::now()->toDateString())->count();
            $view->with('todayOrders', $todayOrders);
        });
        view()->composer('admin.layout.header', function ($view) {
            $todayUsers = User::whereDate('created_at', Carbon::now()->toDateString())->count();
            $view->with('todayUsers', $todayUsers);
        });

        Order::observe(OrderObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
