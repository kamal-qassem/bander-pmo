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
	
	// Invoice Tasks.
    Route::get('invoice_tasks/{invoice_id}', ['uses' => 'Admin\InvoiceTasksController@index', 'as' => 'invoice_tasks.index'] );
	Route::get('invoice_tasks/{invoice_id}/create', ['uses' => 'Admin\InvoiceTasksController@create', 'as' => 'invoice_tasks.create'] );
	Route::post('invoice_tasks/{invoice_id}/create', ['uses' => 'Admin\InvoiceTasksController@store', 'as' => 'invoice_tasks.store'] );
	
	Route::get('invoice_tasks/{invoice_id}/edit/{id}', ['uses' => 'Admin\InvoiceTasksController@edit', 'as' => 'invoice_tasks.edit'] );
	Route::put('invoice_tasks/{invoice_id}/edit/{id}', ['uses' => 'Admin\InvoiceTasksController@update', 'as' => 'invoice_tasks.update'] );
	
	Route::get('invoice_tasks/{invoice_id}/show/{id}', ['uses' => 'Admin\InvoiceTasksController@show', 'as' => 'invoice_tasks.show'] );
	
	Route::delete('invoice_tasks/{invoice_id}/{id}', ['uses' => 'Admin\InvoiceTasksController@destroy', 'as' => 'invoice_tasks.destroy']);
	
	Route::post('invoice_tasks_mass_destroy', ['uses' => 'Admin\InvoiceTasksController@massDestroy', 'as' => 'invoice_tasks.mass_destroy']);
    Route::post('invoice_tasks_restore/{id}', ['uses' => 'Admin\InvoiceTasksController@restore', 'as' => 'invoice_tasks.restore']);
    Route::delete('invoice_tasks_perma_del/{id}', ['uses' => 'Admin\InvoiceTasksController@perma_del', 'as' => 'invoice_tasks.perma_del']);

    Route::get('invoice_tasks/changestatus/{invoice_id}/{id}/{status}', ['uses' => 'Admin\InvoiceTasksController@taskChangestatus', 'as' => 'invoice_tasks.changestatus'] );
	
	// Invoice reminders.
	Route::get('invoice_reminders/{invoice_id}', ['uses' => 'Admin\InvoiceRemindersController@index', 'as' => 'invoice_reminders.index'] );
	Route::get('invoice_reminders/{invoice_id}/create', ['uses' => 'Admin\InvoiceRemindersController@create', 'as' => 'invoice_reminders.create'] );
	Route::post('invoice_reminders/{invoice_id}/create', ['uses' => 'Admin\InvoiceRemindersController@store', 'as' => 'invoice_reminders.store'] );
	
	Route::get('invoice_reminders/{invoice_id}/edit/{id}', ['uses' => 'Admin\InvoiceRemindersController@edit', 'as' => 'invoice_reminders.edit'] );
	Route::put('invoice_reminders/{invoice_id}/edit/{id}', ['uses' => 'Admin\InvoiceRemindersController@update', 'as' => 'invoice_reminders.update'] );
	
	Route::get('invoice_reminders/{invoice_id}/show/{id}', ['uses' => 'Admin\InvoiceRemindersController@show', 'as' => 'invoice_reminders.show'] );
	
	Route::delete('invoice_reminders/{invoice_id}/{id}', ['uses' => 'Admin\InvoiceRemindersController@destroy', 'as' => 'invoice_reminders.destroy']);
	
	Route::post('invoice_reminders_mass_destroy', ['uses' => 'Admin\InvoiceRemindersController@massDestroy', 'as' => 'invoice_reminders.mass_destroy']);
    Route::post('invoice_reminders_restore/{id}', ['uses' => 'Admin\InvoiceRemindersController@restore', 'as' => 'invoice_reminders.restore']);
    Route::delete('invoice_reminders_perma_del/{id}', ['uses' => 'Admin\InvoiceRemindersController@perma_del', 'as' => 'invoice_reminders.perma_del']);

    // Invoice notes.
	Route::get('invoice_notes/{invoice_id}', ['uses' => 'Admin\InvoiceNotesController@index', 'as' => 'invoice_notes.index'] );
	Route::get('invoice_notes/{invoice_id}/create', ['uses' => 'Admin\InvoiceNotesController@create', 'as' => 'invoice_notes.create'] );
	Route::post('invoice_notes/{invoice_id}/create', ['uses' => 'Admin\InvoiceNotesController@store', 'as' => 'invoice_notes.store'] );
	
	Route::get('invoice_notes/{invoice_id}/edit/{id}', ['uses' => 'Admin\InvoiceNotesController@edit', 'as' => 'invoice_notes.edit'] );
	Route::put('invoice_notes/{invoice_id}/edit/{id}', ['uses' => 'Admin\InvoiceNotesController@update', 'as' => 'invoice_notes.update'] );
	
	Route::get('invoice_notes/{invoice_id}/show/{id}', ['uses' => 'Admin\InvoiceNotesController@show', 'as' => 'invoice_notes.show'] );
	
	Route::delete('invoice_notes/{invoice_id}/{id}', ['uses' => 'Admin\InvoiceNotesController@destroy', 'as' => 'invoice_notes.destroy']);
	
	Route::post('invoice_notes_mass_destroy', ['uses' => 'Admin\InvoiceNotesController@massDestroy', 'as' => 'invoice_notes.mass_destroy']);
    Route::post('invoice_notes_restore/{id}', ['uses' => 'Admin\InvoiceNotesController@restore', 'as' => 'invoice_notes.restore']);
    Route::delete('invoice_notes_perma_del/{id}', ['uses' => 'Admin\InvoiceNotesController@perma_del', 'as' => 'invoice_notes.perma_del']);
});
