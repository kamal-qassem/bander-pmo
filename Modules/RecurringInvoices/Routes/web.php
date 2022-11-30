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
	Route::resource('recurring_invoices', 'Admin\RecurringInvoicesController');
    Route::get('list-recurring_invoices/{type?}/{type_id?}', [ 'uses' => 'Admin\RecurringInvoicesController@index', 'as' => 'list_recurring_invoices.index'] );
    Route::post('recurring_invoices_mass_destroy', ['uses' => 'Admin\RecurringInvoicesController@massDestroy', 'as' => 'recurring_invoices.mass_destroy']);
    Route::post('recurring_invoices_restore/{id}', ['uses' => 'Admin\RecurringInvoicesController@restore', 'as' => 'recurring_invoices.restore']);
    Route::delete('recurring_invoices_perma_del/{id}', ['uses' => 'Admin\RecurringInvoicesController@perma_del', 'as' => 'recurring_invoices.perma_del']);

    Route::post('recurring_invoices/send', 'Admin\RecurringInvoicesController@invoiceSend');
    Route::post('recurring_invoices/save-payment', 'Admin\RecurringInvoicesController@savePayment');
    Route::get('recurring_invoices/changestatus/{id}/{status}', [ 'uses' => 'Admin\RecurringInvoicesController@changeStatus', 'as' => 'recurring_invoices.changestatus'] );
    Route::post('recurring_invoices/mail-invoice', ['uses' => 'Admin\RecurringInvoicesController@mailInvoice', 'as' => 'recurring_invoices.mail_invoice']);
    Route::get('recurring_invoices/preview/{slug}', [ 'uses' => 'Admin\RecurringInvoicesController@showPreview', 'as' => 'recurring_invoices.preview'] );
    Route::get('recurring_invoices/invoicepdf/{slug}/{operation?}', [ 'uses' => 'Admin\RecurringInvoicesController@invoicePDF', 'as' => 'recurring_invoices.invoicepdf'] );
    Route::get('recurring_invoices/upload/{slug}', [ 'uses' => 'Admin\RecurringInvoicesController@uploadDocuments', 'as' => 'recurring_invoices.upload'] );
    Route::post('recurring_invoices/upload/{slug}', [ 'uses' => 'Admin\RecurringInvoicesController@upload', 'as' => 'recurring_invoices.process-upload'] );
    Route::get('recurring_invoices/duplicate/{slug}', [ 'uses' => 'Admin\RecurringInvoicesController@duplicate', 'as' => 'recurring_invoices.duplicate'] );

    Route::post('recurring_invoices/refresh-stats', [ 'uses' => 'Admin\RecurringInvoicesController@refreshStats', 'as' => 'recurring_invoices.refresh-stats'] );
	Route::get('recurring-invoice/get-details/{id}', [ 'uses' => 'Admin\RecurringInvoicesController@getDetails', 'as' => 'recurring_invoices.get-details'] );

    Route::get('recurring-invoices/childs/{id}', ['uses' => 'Admin\RecurringInvoicesController@childInvoices', 'as' => 'recurring-invoices.child-invoices']);
});

