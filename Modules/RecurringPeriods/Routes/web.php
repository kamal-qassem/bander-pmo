<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
	Route::resource('recurring_periods', 'Admin\RecurringPeriodsController');
    Route::post('recurring_periods_mass_destroy', ['uses' => 'Admin\RecurringPeriodsController@massDestroy', 'as' => 'recurring_periods.mass_destroy']);
    Route::post('recurring_periods_restore/{id}', ['uses' => 'Admin\RecurringPeriodsController@restore', 'as' => 'recurring_periods.restore']);
    Route::delete('recurring_periods_perma_del/{id}', ['uses' => 'Admin\RecurringPeriodsController@perma_del', 'as' => 'recurring_periods.perma_del']);
});
