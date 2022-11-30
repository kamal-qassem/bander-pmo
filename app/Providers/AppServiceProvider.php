<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\DuskServiceProvider;
use Validator;
// use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        \Laravel\Passport\Passport::withoutCookieSerialization();
        if (\Cookie::get('language')) { \App::setLocale(\Crypt::decrypt(\Cookie::get('language'), false));}
        
        if( env('DB_DATABASE') != '' && ! defined('CRON_JOB') ) {
            $addnew_type = getSetting('addnew_type', 'site_settings', 'symbol');
            view()->share('addnew_type', $addnew_type);

            $show_page_heading = getSetting('show_page_heading', 'local_settings', 'no');
            view()->share('show_page_heading', $show_page_heading);

            config(['app.datetime_format_moment' => getCurrentDateFormat('moment')]);
        } else {
            view()->share('addnew_type', 'symbol');
            view()->share('show_page_heading', 'yes');
            //config(['app.datetime_format_moment' => getCurrentDateFormat('moment')]);
        }

        Validator::extend('productinwarehouse', function($attribute, $value, $parameters) {
            $selected_products = request('product');
            if ( ! empty( $selected_products ) && ! empty( $value ) ) {
                foreach ($selected_products as $product_id) {
                    $product = \App\Product::where('id', $product_id)->where('ware_house_id', $value)->first();
                    if ( ! $product ) {
                        return false;
                    }
                }
            }
            return true;
        });

        Validator::extend('phone_number', function($attribute, $value, $parameters, $validator) {
            return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $value) && strlen($value) >= 10;
        });

        Validator::replacer('phone_number', function($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute',$attribute, ':attribute is invalid phone number');
        });  
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        /**
         * Added missing method for package to work
         */
        \Illuminate\Support\Collection::macro('lists', function ($a, $b = null) {
            return collect($this->items)->pluck($a, $b);
        });

        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }



    }
}
