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
        config(['app.locale' => 'id']);
        \Carbon\Carbon::setLocale('id');

        $school = \App\Models\SchoolProfile::first();
        \Illuminate\Support\Facades\View::share('school', $school);

        $is_pmb_open = \App\Models\Setting::isPmbOpen();
        \Illuminate\Support\Facades\View::share('is_pmb_open', $is_pmb_open);
    }
}
