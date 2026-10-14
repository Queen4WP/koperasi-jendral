<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $user = (request()->is('admin*') && auth('admin')->check())
                ? auth('admin')->user()
                : (auth('web')->check() ? auth('web')->user() : auth()->user());

            if ($user) {
                $userNotifications = \App\Models\Notification::where('user_id', $user->id)
                    ->latest()
                    ->take(15)
                    ->get();
                $unreadNotificationsCount = \App\Models\Notification::where('user_id', $user->id)
                    ->unread()
                    ->count();

                $view->with(compact('userNotifications', 'unreadNotificationsCount'));
            }
        });
    }
}
