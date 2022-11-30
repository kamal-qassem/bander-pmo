<?php

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {

        Route::resource('modules_management', 'ModulesManagementsController', ['except' => ['create', 'edit']]);

});
