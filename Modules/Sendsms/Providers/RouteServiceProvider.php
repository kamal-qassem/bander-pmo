<?php

namespace Modules\Sendsms\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Eventy;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = 'Modules\Sendsms\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

       
        Eventy::addAction('master_settings.destroy', function($id) {
            
            $master_setting = \App\MasterSetting::withTrashed()->find($id);

            if ( $master_setting && 'sms' === $master_setting->moduletype ) {
                $gateway = \Modules\Sendsms\Entities\SmsGateway::where('key', '=', $master_setting->key )->first();
                if ( $gateway ) {
                    $gateway->delete();
                }
            }
        }, 20, 1);

        Eventy::addAction('master_settings.massdestroy', function($request) {
            $entries = \App\MasterSetting::whereIn('id', $request->input('ids'))->withTrashed()->get();
            if ( $entries ) {
                foreach ($entries as $entry) {
                    if ( 'sms' === $entry->moduletype ) {
                        $entry->delete();
                    }
                }
            }
        }, 20, 1);

        Eventy::addAction('master_settings.restore', function($id) {
            
            $master_setting = \App\MasterSetting::withTrashed()->find($id);

            if ( $master_setting && 'sms' === $master_setting->moduletype ) {
                $gateway = \Modules\Sendsms\Entities\SmsGateway::where('key', '=', $master_setting->key )->withTrashed()->first();
                if ( $gateway ) {
                    $gateway->restore();
                }
            }
        }, 20, 1);

        Eventy::addAction('master_settings.perma_del_before', function($id) {
            $master_setting = \App\MasterSetting::withTrashed()->find($id);
            
            if ( $master_setting && 'sms' === $master_setting->moduletype ) {
                $gateway = \Modules\Sendsms\Entities\SmsGateway::where('key', '=', $master_setting->key )->withTrashed()->first();
                if ( $gateway ) {
                    $gateway->forceDelete();
                }
            }
        }, 20, 1);
    }

    public function updateSmsGateways() {
        $sms_gateways = \App\Settings::where('moduletype', 'sms')->get()->pluck('module', 'key');
        if ( ! empty( $sms_gateways ) ) {
            foreach ($sms_gateways as $key => $value) {
               $gateway = \Modules\Sendsms\Entities\SmsGateway::firstOrNew( ['key' => $key ] );
               $gateway->key = $key;
               $gateway->name = $value;
               $gateway->description = $value;
               $gateway->save();
            }
        }
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
