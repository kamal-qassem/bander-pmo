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
	Route::resource('templates', 'TemplatesController');
    Route::post('templates_mass_destroy', ['uses' => 'TemplatesController@massDestroy', 'as' => 'templates.mass_destroy']);
    Route::post('templates_restore/{id}', ['uses' => 'TemplatesController@restore', 'as' => 'templates.restore']);
    Route::delete('templates_perma_del/{id}', ['uses' => 'TemplatesController@perma_del', 'as' => 'templates.perma_del']);
    Route::get('template/duplicate/{id}', [ 'uses' => 'TemplatesController@duplicate', 'as' => 'templates.duplicate' ]);
});
