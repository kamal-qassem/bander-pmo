<?php
Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        
    Route::resource('sms_gateways', 'Admin\SmsGatewaysController');
    Route::post('sms_gateways_mass_destroy', ['uses' => 'Admin\SmsGatewaysController@massDestroy', 'as' => 'sms_gateways.mass_destroy']);
    Route::post('sms_gateways_restore/{id}', ['uses' => 'Admin\SmsGatewaysController@restore', 'as' => 'sms_gateways.restore']);
    Route::delete('sms_gateways_perma_del/{id}', ['uses' => 'Admin\SmsGatewaysController@perma_del', 'as' => 'sms_gateways.perma_del']);
    
    Route::resource('send_sms', 'Admin\SendSmsController');
    Route::post('send_sms_mass_destroy', ['uses' => 'Admin\SendSmsController@massDestroy', 'as' => 'send_sms.mass_destroy']);
    Route::post('send_sms_restore/{id}', ['uses' => 'Admin\SendSmsController@restore', 'as' => 'send_sms.restore']);
    Route::delete('send_sms_perma_del/{id}', ['uses' => 'Admin\SendSmsController@perma_del', 'as' => 'send_sms.perma_del']);
    Route::post('send_sms/getuserbyid', ['uses' => 'Admin\SendSmsController@getUserById', 'as' => 'sendsms.getuserbyid']);
});