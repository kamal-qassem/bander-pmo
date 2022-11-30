<?php
Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        
    Route::resource('modules_managements', 'Admin\ModulesManagementsController');
    Route::get('modules/availableplugins', 'Admin\ModulesManagementsController@availablePlugins');
    Route::get('plugins/upload', ['uses' => 'Admin\ModulesManagementsController@upload', 'as' => 'plugins.upload']);
    Route::post('plugins/upload-store', ['uses' => 'Admin\ModulesManagementsController@uploadStore', 'as' => 'plugins.upload_store']);

    
    Route::post('modules_managements_mass_destroy', ['uses' => 'Admin\ModulesManagementsController@massDestroy', 'as' => 'modules_managements.mass_destroy']);
    Route::post('modules_managements_restore/{id}', ['uses' => 'Admin\ModulesManagementsController@restore', 'as' => 'modules_managements.restore']);
    Route::delete('modules_managements_perma_del/{id}', ['uses' => 'Admin\ModulesManagementsController@perma_del', 'as' => 'modules_managements.perma_del']);

    Route::get('modules-managements/changestatus/{id}', [ 'uses' => 'Admin\ModulesManagementsController@changeStatus', 'as' => 'modules-management.changestatus'] );

    Route::get('plugin/settings/{slug}', ['uses' => 'Admin\ModulesManagementsController@viewSettings', 'as' => 'site_plugins.viewsettings']);
    Route::post('plugin/update/settings/{slug}', ['uses' => 'Admin\ModulesManagementsController@updateSubSettings', 'as' => 'site_plugins.updatesettings']);
	Route::get('plugin/settings/add-sub-settings/{slug}', ['uses' => 'Admin\ModulesManagementsController@addSubSettings', 'as' => 'site_plugins.add-sub-settings']);
    Route::post('plugin/settings/add-sub-settings/{slug}', ['uses' => 'Admin\ModulesManagementsController@storeSubSettings', 'as' => 'site_plugins.add-sub-settings-store']);
    Route::patch('plugin/settings/add-sub-settings/{slug}', ['uses' => 'Admin\ModulesManagementsController@updateSubSettings', 'as' => 'site_plugins.add-sub-settings-update']);
    
 });
