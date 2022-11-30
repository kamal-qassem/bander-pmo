<?php
Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
       
    Route::resource('site_themes', 'Admin\SiteThemesController');
    Route::post('site_themes_mass_destroy', ['uses' => 'Admin\SiteThemesController@massDestroy', 'as' => 'site_themes.mass_destroy']);
    Route::post('site_themes_restore/{id}', ['uses' => 'Admin\SiteThemesController@restore', 'as' => 'site_themes.restore']);
    Route::delete('site_themes_perma_del/{id}', ['uses' => 'Admin\SiteThemesController@perma_del', 'as' => 'site_themes.perma_del']);

	Route::get('make/default/theme/{id}', ['uses' => 'Admin\SiteThemesController@makeDefault', 'as' => 'site_themes.makedefault']);
	Route::get('theme/settings/{slug}', ['uses' => 'Admin\SiteThemesController@viewSettings', 'as' => 'site_themes.viewsettings']);
	Route::post('theme/update/settings/{slug}', ['uses' => 'Admin\SiteThemesController@updateSubSettings', 'as' => 'site_themes.updatesettings']);

	Route::get('theme/settings/add-sub-settings/{slug}', ['uses' => 'Admin\SiteThemesController@addSubSettings', 'as' => 'site_themes.add-sub-settings']);
    Route::post('theme/settings/add-sub-settings/{slug}', ['uses' => 'Admin\SiteThemesController@storeSubSettings', 'as' => 'site_themes.add-sub-settings-store']);
    Route::patch('theme/settings/add-sub-settings/{slug}', ['uses' => 'Admin\SiteThemesController@updateSubSettings', 'as' => 'site_themes.add-sub-settings-update']);
    
});
