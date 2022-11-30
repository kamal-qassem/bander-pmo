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
	Route::resource('dynamic_options', 'Admin\DynamicOptionsController');
    Route::post('dynamic_options_mass_destroy', ['uses' => 'Admin\DynamicOptionsController@massDestroy', 'as' => 'dynamic_options.mass_destroy']);
    Route::post('dynamic_options_restore/{id}', ['uses' => 'Admin\DynamicOptionsController@restore', 'as' => 'dynamic_options.restore']);
    Route::delete('dynamic_options_perma_del/{id}', ['uses' => 'Admin\DynamicOptionsController@perma_del', 'as' => 'dynamic_options.perma_del']);
});
