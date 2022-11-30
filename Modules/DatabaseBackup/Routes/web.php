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
	Route::resource('database_backups', 'Admin\DatabaseBackupsController');
	Route::delete('databasebackups/delete/{id}/{type}', [ 'uses' => 'Admin\DatabaseBackupsController@destroy', 'as' => 'databasebackups.delete'] );

	Route::get('databasebackups/{type}', [ 'uses' => 'Admin\DatabaseBackupsController@index', 'as' => 'databasebackups.index']);
    Route::post('database_backups_mass_destroy', ['uses' => 'Admin\DatabaseBackupsController@massDestroy', 'as' => 'database_backups.mass_destroy']);
    Route::post('database_backups_restore/{id}', ['uses' => 'Admin\DatabaseBackupsController@restore', 'as' => 'database_backups.restore']);
    Route::delete('database_backups_perma_del/{id}', ['uses' => 'Admin\DatabaseBackupsController@perma_del', 'as' => 'database_backups.perma_del']);
    Route::get('backup/download/{name}', ['uses' => 'Admin\DatabaseBackupsController@download', 'as' => 'database_backups.download']);

    Route::get('backup/delete/{file_name}', ['uses' => 'Admin\DatabaseBackupsController@delete', 'as' => 'database_backups.delete']);

});
