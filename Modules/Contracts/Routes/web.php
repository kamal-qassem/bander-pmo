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
	Route::resource('contracts', 'Admin\ContractsController');
	Route::get('list-contracts/{type?}/{type_id?}', [ 'uses' => 'Admin\ContractsController@index', 'as' => 'list-contracts.index'] );
	
    Route::post('contracts_mass_destroy', ['uses' => 'Admin\ContractsController@massDestroy', 'as' => 'contracts.mass_destroy']);
    Route::post('contracts_restore/{id}', ['uses' => 'Admin\ContractsController@restore', 'as' => 'contracts.restore']);
    Route::delete('contracts_perma_del/{id}', ['uses' => 'Admin\ContractsController@perma_del', 'as' => 'contracts.perma_del']);

    Route::post('contracts/send', 'Admin\ContractsController@invoiceSend');
    Route::post('contracts/save-payment', 'Admin\ContractsController@savePayment');
    Route::get('contracts/changestatus/{id}/{status}', [ 'uses' => 'Admin\ContractsController@changeStatus', 'as' => 'contracts.changestatus'] );
    Route::post('contracts/mail-invoice', ['uses' => 'Admin\ContractsController@mailInvoice', 'as' => 'contracts.mail_invoice']);
    Route::get('contracts/preview/{slug}', [ 'uses' => 'Admin\ContractsController@showPreview', 'as' => 'contracts.preview'] );
    Route::get('contracts/invoicepdf/{slug}/{operation?}', [ 'uses' => 'Admin\ContractsController@invoicePDF', 'as' => 'contracts.invoicepdf'] );
    Route::get('contracts/upload/{slug}', [ 'uses' => 'Admin\ContractsController@uploadDocuments', 'as' => 'contracts.upload'] );
    Route::post('contracts/upload/{slug}', [ 'uses' => 'Admin\ContractsController@upload', 'as' => 'contracts.process-upload'] );
    Route::get('contracts/duplicate/{slug}', [ 'uses' => 'Admin\ContractsController@duplicate', 'as' => 'contracts.duplicate'] );

    Route::get('contracts/convert-to-invoice/{slug}/{type?}', [ 'uses' => 'Admin\ContractsController@convertToInvoice', 'as' => 'contracts.convertinvoice'] );
    // Contract Types.
    Route::resource('contract_types', 'Admin\ContractTypesController');
     Route::post('contract_types_mass_destroy', ['uses' => 'Admin\ContractTypesController@massDestroy', 'as' => 'contract_types.mass_destroy']);
	// Contract Tasks.
    Route::get('contract_tasks/{contract_id}', ['uses' => 'Admin\ContractTasksController@index', 'as' => 'contract_tasks.index'] );
	Route::get('contract_tasks/{contract_id}/create', ['uses' => 'Admin\ContractTasksController@create', 'as' => 'contract_tasks.create'] );
	Route::post('contract_tasks/{contract_id}/create', ['uses' => 'Admin\ContractTasksController@store', 'as' => 'contract_tasks.store'] );
	
	Route::get('contract_tasks/{contract_id}/edit/{id}', ['uses' => 'Admin\ContractTasksController@edit', 'as' => 'contract_tasks.edit'] );
	Route::put('contract_tasks/{contract_id}/edit/{id}', ['uses' => 'Admin\ContractTasksController@update', 'as' => 'contract_tasks.update'] );
	
	Route::get('contract_tasks/{contract_id}/show/{id}', ['uses' => 'Admin\ContractTasksController@show', 'as' => 'contract_tasks.show'] );
	
	Route::delete('contract_tasks/{contract_id}/{id}', ['uses' => 'Admin\ContractTasksController@destroy', 'as' => 'contract_tasks.destroy']);
	
	Route::post('contract_tasks_mass_destroy', ['uses' => 'Admin\ContractTasksController@massDestroy', 'as' => 'contract_tasks.mass_destroy']);
    Route::post('contract_tasks_restore/{id}', ['uses' => 'Admin\ContractTasksController@restore', 'as' => 'contract_tasks.restore']);
    Route::delete('contract_tasks_perma_del/{id}', ['uses' => 'Admin\ContractTasksController@perma_del', 'as' => 'contract_tasks.perma_del']);

    Route::get('contract_tasks/changestatus/{contract_id}/{id}/{status}', ['uses' => 'Admin\ContractTasksController@taskChangestatus', 'as' => 'contract_tasks.changestatus'] );
	
	// Contracts reminders.
	Route::get('contract_reminders/{contract_id}', ['uses' => 'Admin\ContractsRemindersController@index', 'as' => 'contract_reminders.index'] );
	Route::get('contract_reminders/{contract_id}/create', ['uses' => 'Admin\ContractsRemindersController@create', 'as' => 'contract_reminders.create'] );
	Route::post('contract_reminders/{contract_id}/create', ['uses' => 'Admin\ContractsRemindersController@store', 'as' => 'contract_reminders.store'] );
	
	Route::get('contract_reminders/{contract_id}/edit/{id}', ['uses' => 'Admin\ContractsRemindersController@edit', 'as' => 'contract_reminders.edit'] );
	Route::put('contract_reminders/{contract_id}/edit/{id}', ['uses' => 'Admin\ContractsRemindersController@update', 'as' => 'contract_reminders.update'] );
	
	Route::get('contract_reminders/{contract_id}/show/{id}', ['uses' => 'Admin\ContractsRemindersController@show', 'as' => 'contract_reminders.show'] );
	
	Route::delete('contract_reminders/{contract_id}/{id}', ['uses' => 'Admin\ContractsRemindersController@destroy', 'as' => 'contract_reminders.destroy']);
	
	Route::post('contract_reminders_mass_destroy', ['uses' => 'Admin\ContractsRemindersController@massDestroy', 'as' => 'contract_reminders.mass_destroy']);
    Route::post('contract_reminders_restore/{id}', ['uses' => 'Admin\ContractsRemindersController@restore', 'as' => 'contract_reminders.restore']);
    Route::delete('contract_reminders_perma_del/{id}', ['uses' => 'Admin\ContractsRemindersController@perma_del', 'as' => 'contract_reminders.perma_del']);

    // Contracts notes.
	Route::get('contracts_notes/{contract_id}', ['uses' => 'Admin\ContractsNotesController@index', 'as' => 'contracts_notes.index'] );
	Route::get('contracts_notes/{contract_id}/create', ['uses' => 'Admin\ContractsNotesController@create', 'as' => 'contracts_notes.create'] );
	Route::post('contracts_notes/{contract_id}/create', ['uses' => 'Admin\ContractsNotesController@store', 'as' => 'contracts_notes.store'] );
	
	Route::get('contracts_notes/{contract_id}/edit/{id}', ['uses' => 'Admin\ContractsNotesController@edit', 'as' => 'contracts_notes.edit'] );
	Route::put('contracts_notes/{contract_id}/edit/{id}', ['uses' => 'Admin\ContractsNotesController@update', 'as' => 'contracts_notes.update'] );
	
	Route::get('contracts_notes/{contract_id}/show/{id}', ['uses' => 'Admin\ContractsNotesController@show', 'as' => 'contracts_notes.show'] );
	
	Route::delete('contracts_notes/{contract_id}/{id}', ['uses' => 'Admin\ContractsNotesController@destroy', 'as' => 'contracts_notes.destroy']);
	
	Route::post('contracts_notes_mass_destroy', ['uses' => 'Admin\ContractsNotesController@massDestroy', 'as' => 'contracts_notes.mass_destroy']);
    Route::post('contracts_notes_restore/{id}', ['uses' => 'Admin\ContractsNotesController@restore', 'as' => 'contracts_notes.restore']);
    Route::delete('contracts_notes_perma_del/{id}', ['uses' => 'Admin\ContractsNotesController@perma_del', 'as' => 'contracts_notes.perma_del']);
});




