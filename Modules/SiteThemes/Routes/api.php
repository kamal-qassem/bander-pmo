<?php

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
        
        Route::resource('site_themes', 'SiteThemesController', ['except' => ['create', 'edit']]);

});
