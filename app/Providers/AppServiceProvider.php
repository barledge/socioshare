<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if(env('REDIRECT_HTTPS')) {
            //URL::forceScheme('https');
        }

        try {
            $timezone = Setting::where('key', 'timezone')->first();
            if($timezone) {
                config(['app.timezone' => $timezone->value]);
            }
        } catch (\Exception $e ) {

        }

		Schema::defaultStringLength(191);
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
