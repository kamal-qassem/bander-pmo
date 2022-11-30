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
	Route::resource('proposals', 'Admin\ProposalsController');
	Route::get('list-proposals/{type?}/{type_id?}', [ 'uses' => 'Admin\ProposalsController@index', 'as' => 'list-proposals.index'] );
	
    Route::post('proposals_mass_destroy', ['uses' => 'Admin\ProposalsController@massDestroy', 'as' => 'proposals.mass_destroy']);
    Route::post('proposals_restore/{id}', ['uses' => 'Admin\ProposalsController@restore', 'as' => 'proposals.restore']);
    Route::delete('proposals_perma_del/{id}', ['uses' => 'Admin\ProposalsController@perma_del', 'as' => 'proposals.perma_del']);

    Route::post('proposals/send', 'Admin\ProposalsController@invoiceSend');
    Route::post('proposals/save-payment', 'Admin\ProposalsController@savePayment');
    Route::get('proposals/changestatus/{id}/{status}', [ 'uses' => 'Admin\ProposalsController@changeStatus', 'as' => 'proposals.changestatus'] );
    Route::post('proposals/mail-invoice', ['uses' => 'Admin\ProposalsController@mailInvoice', 'as' => 'proposals.mail_invoice']);
    Route::get('proposals/preview/{slug}', [ 'uses' => 'Admin\ProposalsController@showPreview', 'as' => 'proposals.preview'] );
    Route::get('proposals/invoicepdf/{slug}/{operation?}', [ 'uses' => 'Admin\ProposalsController@invoicePDF', 'as' => 'proposals.invoicepdf'] );
    Route::get('proposals/upload/{slug}', [ 'uses' => 'Admin\ProposalsController@uploadDocuments', 'as' => 'proposals.upload'] );
    Route::post('proposals/upload/{slug}', [ 'uses' => 'Admin\ProposalsController@upload', 'as' => 'proposals.process-upload'] );
    Route::get('proposals/duplicate/{slug}', [ 'uses' => 'Admin\ProposalsController@duplicate', 'as' => 'proposals.duplicate'] );

    Route::get('proposals/convert-to-invoice/{slug}/{type?}', [ 'uses' => 'Admin\ProposalsController@convertToInvoice', 'as' => 'proposals.convertinvoice'] );
    Route::get('proposals/convert-to-quotes/{slug}/{type?}', [ 'uses' => 'Admin\ProposalsController@convertToQuote', 'as' => 'proposals.convertquote'] );
    
	// Proposal Tasks.
    Route::get('proposal_tasks/{proposal_id}', ['uses' => 'Admin\ProposalTasksController@index', 'as' => 'proposal_tasks.index'] );
	Route::get('proposal_tasks/{proposal_id}/create', ['uses' => 'Admin\ProposalTasksController@create', 'as' => 'proposal_tasks.create'] );
	Route::post('proposal_tasks/{proposal_id}/create', ['uses' => 'Admin\ProposalTasksController@store', 'as' => 'proposal_tasks.store'] );
	
	Route::get('proposal_tasks/{proposal_id}/edit/{id}', ['uses' => 'Admin\ProposalTasksController@edit', 'as' => 'proposal_tasks.edit'] );
	Route::put('proposal_tasks/{proposal_id}/edit/{id}', ['uses' => 'Admin\ProposalTasksController@update', 'as' => 'proposal_tasks.update'] );
	
	Route::get('proposal_tasks/{proposal_id}/show/{id}', ['uses' => 'Admin\ProposalTasksController@show', 'as' => 'proposal_tasks.show'] );
	
	Route::delete('proposal_tasks/{proposal_id}/{id}', ['uses' => 'Admin\ProposalTasksController@destroy', 'as' => 'proposal_tasks.destroy']);
	
	Route::post('proposal_tasks_mass_destroy', ['uses' => 'Admin\ProposalTasksController@massDestroy', 'as' => 'proposal_tasks.mass_destroy']);
    Route::post('proposal_tasks_restore/{id}', ['uses' => 'Admin\ProposalTasksController@restore', 'as' => 'proposal_tasks.restore']);
    Route::delete('proposal_tasks_perma_del/{id}', ['uses' => 'Admin\ProposalTasksController@perma_del', 'as' => 'proposal_tasks.perma_del']);

    Route::get('proposal_tasks/changestatus/{proposal_id}/{id}/{status}', ['uses' => 'Admin\ProposalTasksController@taskChangestatus', 'as' => 'proposal_tasks.changestatus'] );
	
	// Proposals reminders.
	Route::get('proposal_reminders/{proposal_id}', ['uses' => 'Admin\ProposalsRemindersController@index', 'as' => 'proposal_reminders.index'] );
	Route::get('proposal_reminders/{proposal_id}/create', ['uses' => 'Admin\ProposalsRemindersController@create', 'as' => 'proposal_reminders.create'] );
	Route::post('proposal_reminders/{proposal_id}/create', ['uses' => 'Admin\ProposalsRemindersController@store', 'as' => 'proposal_reminders.store'] );
	
	Route::get('proposal_reminders/{proposal_id}/edit/{id}', ['uses' => 'Admin\ProposalsRemindersController@edit', 'as' => 'proposal_reminders.edit'] );
	Route::put('proposal_reminders/{proposal_id}/edit/{id}', ['uses' => 'Admin\ProposalsRemindersController@update', 'as' => 'proposal_reminders.update'] );
	
	Route::get('proposal_reminders/{proposal_id}/show/{id}', ['uses' => 'Admin\ProposalsRemindersController@show', 'as' => 'proposal_reminders.show'] );
	
	Route::delete('proposal_reminders/{proposal_id}/{id}', ['uses' => 'Admin\ProposalsRemindersController@destroy', 'as' => 'proposal_reminders.destroy']);
	
	Route::post('proposal_reminders_mass_destroy', ['uses' => 'Admin\ProposalsRemindersController@massDestroy', 'as' => 'proposal_reminders.mass_destroy']);
    Route::post('proposal_reminders_restore/{id}', ['uses' => 'Admin\ProposalsRemindersController@restore', 'as' => 'proposal_reminders.restore']);
    Route::delete('proposal_reminders_perma_del/{id}', ['uses' => 'Admin\ProposalsRemindersController@perma_del', 'as' => 'proposal_reminders.perma_del']);

    // Proposals notes.
	Route::get('proposals_notes/{proposal_id}', ['uses' => 'Admin\ProposalsNotesController@index', 'as' => 'proposals_notes.index'] );
	Route::get('proposals_notes/{proposal_id}/create', ['uses' => 'Admin\ProposalsNotesController@create', 'as' => 'proposals_notes.create'] );
	Route::post('proposals_notes/{proposal_id}/create', ['uses' => 'Admin\ProposalsNotesController@store', 'as' => 'proposals_notes.store'] );
	
	Route::get('proposals_notes/{proposal_id}/edit/{id}', ['uses' => 'Admin\ProposalsNotesController@edit', 'as' => 'proposals_notes.edit'] );
	Route::put('proposals_notes/{proposal_id}/edit/{id}', ['uses' => 'Admin\ProposalsNotesController@update', 'as' => 'proposals_notes.update'] );
	
	Route::get('proposals_notes/{proposal_id}/show/{id}', ['uses' => 'Admin\ProposalsNotesController@show', 'as' => 'proposals_notes.show'] );
	
	Route::delete('proposals_notes/{proposal_id}/{id}', ['uses' => 'Admin\ProposalsNotesController@destroy', 'as' => 'proposals_notes.destroy']);
	
	Route::post('proposals_notes_mass_destroy', ['uses' => 'Admin\ProposalsNotesController@massDestroy', 'as' => 'proposals_notes.mass_destroy']);
    Route::post('proposals_notes_restore/{id}', ['uses' => 'Admin\ProposalsNotesController@restore', 'as' => 'proposals_notes.restore']);
    Route::delete('proposals_notes_perma_del/{id}', ['uses' => 'Admin\ProposalsNotesController@perma_del', 'as' => 'proposals_notes.perma_del']);
});


