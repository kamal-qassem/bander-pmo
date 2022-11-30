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
	Route::resource('quotes', 'Admin\QuotesController');
	Route::get('list-quotes/{type?}/{type_id?}', [ 'uses' => 'Admin\QuotesController@index', 'as' => 'list-quotes.index'] );
	
    Route::post('quotes_mass_destroy', ['uses' => 'Admin\QuotesController@massDestroy', 'as' => 'quotes.mass_destroy']);
    Route::post('quotes_restore/{id}', ['uses' => 'Admin\QuotesController@restore', 'as' => 'quotes.restore']);
    Route::delete('quotes_perma_del/{id}', ['uses' => 'Admin\QuotesController@perma_del', 'as' => 'quotes.perma_del']);

    Route::post('quotes/send', 'Admin\QuotesController@invoiceSend');
    Route::post('quotes/save-payment', 'Admin\QuotesController@savePayment');
    Route::get('quotes/changestatus/{id}/{status}', [ 'uses' => 'Admin\QuotesController@changeStatus', 'as' => 'quotes.changestatus'] );
    Route::post('quotes/mail-invoice', ['uses' => 'Admin\QuotesController@mailInvoice', 'as' => 'quotes.mail_invoice']);
    Route::get('quotes/preview/{slug}', [ 'uses' => 'Admin\QuotesController@showPreview', 'as' => 'quotes.preview'] );
    Route::get('quotes/invoicepdf/{slug}/{operation?}', [ 'uses' => 'Admin\QuotesController@invoicePDF', 'as' => 'quotes.invoicepdf'] );
    Route::get('quotes/upload/{slug}', [ 'uses' => 'Admin\QuotesController@uploadDocuments', 'as' => 'quotes.upload'] );
    Route::post('quotes/upload/{slug}', [ 'uses' => 'Admin\QuotesController@upload', 'as' => 'quotes.process-upload'] );
    Route::get('quotes/duplicate/{slug}', [ 'uses' => 'Admin\QuotesController@duplicate', 'as' => 'quotes.duplicate'] );

    Route::get('quotes/convert-to-invoice/{slug}/{type?}', [ 'uses' => 'Admin\QuotesController@convertToInvoice', 'as' => 'quotes.convertinvoice'] );
    
	// Quote Tasks.
    Route::get('quote_tasks/{quote_id}', ['uses' => 'Admin\QuoteTasksController@index', 'as' => 'quote_tasks.index'] );
	Route::get('quote_tasks/{quote_id}/create', ['uses' => 'Admin\QuoteTasksController@create', 'as' => 'quote_tasks.create'] );
	Route::post('quote_tasks/{quote_id}/create', ['uses' => 'Admin\QuoteTasksController@store', 'as' => 'quote_tasks.store'] );
	
	Route::get('quote_tasks/{quote_id}/edit/{id}', ['uses' => 'Admin\QuoteTasksController@edit', 'as' => 'quote_tasks.edit'] );
	Route::put('quote_tasks/{quote_id}/edit/{id}', ['uses' => 'Admin\QuoteTasksController@update', 'as' => 'quote_tasks.update'] );
	
	Route::get('quote_tasks/{quote_id}/show/{id}', ['uses' => 'Admin\QuoteTasksController@show', 'as' => 'quote_tasks.show'] );
	
	Route::delete('quote_tasks/{quote_id}/{id}', ['uses' => 'Admin\QuoteTasksController@destroy', 'as' => 'quote_tasks.destroy']);
	
	Route::post('quote_tasks_mass_destroy', ['uses' => 'Admin\QuoteTasksController@massDestroy', 'as' => 'quote_tasks.mass_destroy']);
    Route::post('quote_tasks_restore/{id}', ['uses' => 'Admin\QuoteTasksController@restore', 'as' => 'quote_tasks.restore']);
    Route::delete('quote_tasks_perma_del/{id}', ['uses' => 'Admin\QuoteTasksController@perma_del', 'as' => 'quote_tasks.perma_del']);

    Route::get('quote_tasks/changestatus/{quote_id}/{id}/{status}', ['uses' => 'Admin\QuoteTasksController@taskChangestatus', 'as' => 'quote_tasks.changestatus'] );
	
	// Quotes reminders.
	Route::get('quote_reminders/{quote_id}', ['uses' => 'Admin\QuotesRemindersController@index', 'as' => 'quote_reminders.index'] );
	Route::get('quote_reminders/{quote_id}/create', ['uses' => 'Admin\QuotesRemindersController@create', 'as' => 'quote_reminders.create'] );
	Route::post('quote_reminders/{quote_id}/create', ['uses' => 'Admin\QuotesRemindersController@store', 'as' => 'quote_reminders.store'] );
	
	Route::get('quote_reminders/{quote_id}/edit/{id}', ['uses' => 'Admin\QuotesRemindersController@edit', 'as' => 'quote_reminders.edit'] );
	Route::put('quote_reminders/{quote_id}/edit/{id}', ['uses' => 'Admin\QuotesRemindersController@update', 'as' => 'quote_reminders.update'] );
	
	Route::get('quote_reminders/{quote_id}/show/{id}', ['uses' => 'Admin\QuotesRemindersController@show', 'as' => 'quote_reminders.show'] );
	
	Route::delete('quote_reminders/{quote_id}/{id}', ['uses' => 'Admin\QuotesRemindersController@destroy', 'as' => 'quote_reminders.destroy']);
	
	Route::post('quote_reminders_mass_destroy', ['uses' => 'Admin\QuotesRemindersController@massDestroy', 'as' => 'quote_reminders.mass_destroy']);
    Route::post('quote_reminders_restore/{id}', ['uses' => 'Admin\QuotesRemindersController@restore', 'as' => 'quote_reminders.restore']);
    Route::delete('quote_reminders_perma_del/{id}', ['uses' => 'Admin\QuotesRemindersController@perma_del', 'as' => 'quote_reminders.perma_del']);

    // Quotes notes.
	Route::get('quotes_notes/{quote_id}', ['uses' => 'Admin\QuotesNotesController@index', 'as' => 'quotes_notes.index'] );
	Route::get('quotes_notes/{quote_id}/create', ['uses' => 'Admin\QuotesNotesController@create', 'as' => 'quotes_notes.create'] );
	Route::post('quotes_notes/{quote_id}/create', ['uses' => 'Admin\QuotesNotesController@store', 'as' => 'quotes_notes.store'] );
	
	Route::get('quotes_notes/{quote_id}/edit/{id}', ['uses' => 'Admin\QuotesNotesController@edit', 'as' => 'quotes_notes.edit'] );
	Route::put('quotes_notes/{quote_id}/edit/{id}', ['uses' => 'Admin\QuotesNotesController@update', 'as' => 'quotes_notes.update'] );
	
	Route::get('quotes_notes/{quote_id}/show/{id}', ['uses' => 'Admin\QuotesNotesController@show', 'as' => 'quotes_notes.show'] );
	
	Route::delete('quotes_notes/{quote_id}/{id}', ['uses' => 'Admin\QuotesNotesController@destroy', 'as' => 'quotes_notes.destroy']);
	
	Route::post('quotes_notes_mass_destroy', ['uses' => 'Admin\QuotesNotesController@massDestroy', 'as' => 'quotes_notes.mass_destroy']);
    Route::post('quotes_notes_restore/{id}', ['uses' => 'Admin\QuotesNotesController@restore', 'as' => 'quotes_notes.restore']);
    Route::delete('quotes_notes_perma_del/{id}', ['uses' => 'Admin\QuotesNotesController@perma_del', 'as' => 'quotes_notes.perma_del']);
});


