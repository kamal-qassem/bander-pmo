<?php
//if( config('app.db_database') == '' )

if( env('DB_DATABASE') == '' )
{
    Route::get('install-instructions', 'Admin\InstallatationController@index')->name('install.index');
    Route::match(['get', 'post'], '/install-check-requiremetns', 'Admin\InstallatationController@requirements')->name('install.requirements');
    Route::match(['get', 'post'], '/install-project', 'Admin\InstallatationController@installProject')->name('install.project');
}
Route::match(['get', 'post'], 'install/register', 'Admin\InstallatationController@registerUser')->name('install.register');

Route::middleware(['install'])->group( function () {

    Route::get('/', function () {
        return redirect('/admin/dashboard');
    });
        Route::get('/signup', function () {
        return view('Auth\signup');
    })->name('signup');
    Route::post('Register', 'Auth\RegisterController@Reg')->name('Register');

    // Authentication Routes...
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@postLogin')->name('auth.login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('direct-login/{role_id?}', 'Auth\LoginController@directLogin')->name('direct.login');

    // Change Password Routes...
    Route::get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
    Route::patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

    // Password Reset Routes...
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

    Route::get('user/activate/{code}', 'Auth\LoginController@confirm')->name('user.activate');

    Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {

        Route::get('/reports/expense-report', 'Admin\ReportsController@expenseReport');
        Route::get('/reports/income-report', 'Admin\ReportsController@incomeReport');
        Route::get('/reports/users-report', 'Admin\ReportsController@usersReport');
        Route::get('/reports/contacts-projects-reports', 'Admin\ReportsController@contactsProjectsReports');
        Route::get('/reports/tasks-report', 'Admin\ReportsController@tasksReport');
        Route::get('/reports/assets-report', 'Admin\ReportsController@assetsReport');
        Route::get('/reports/products-report', 'Admin\ReportsController@productsReport');
        Route::get('/reports/purchase-orders-report', 'Admin\ReportsController@purchaseOrdersReport');
        Route::get('/reports/roles-users-report', 'Admin\ReportsController@rolesUsersReport');

        Route::resource('incomes', 'Admin\IncomesController');
        //Route::get('customer-incomes/{payer_id}', [ 'uses' => 'Admin\IncomesController@index', 'as' => 'customer_incomes.index'] );
        Route::get('list-incomes/{type?}/{type_id?}', [ 'uses' => 'Admin\IncomesController@index', 'as' => 'list_incomes.index'] );
        Route::get('incomes/receipt/{slug}', ['uses' => 'Admin\IncomesController@receipt', 'as' => 'incomes.receipt']);
        Route::post('incomes_mass_destroy', ['uses' => 'Admin\IncomesController@massDestroy', 'as' => 'incomes.mass_destroy']);
        Route::get('incomes/receiptpdf/{slug}/{operation?}', [ 'uses' => 'Admin\IncomesController@receiptPDF', 'as' => 'incomes.receiptpdf'] );
        Route::post('incomes/mail-receipt', ['uses' => 'Admin\IncomesController@mailReceipt', 'as' => 'incomes.mail_receipt']);

        Route::resource('expenses', 'Admin\ExpensesController');
        //Route::get('customer-expenses/{payee_id}', [ 'uses' => 'Admin\ExpensesController@index', 'as' => 'customer_expenses.index'] );
        Route::get('list-expenses/{type?}/{type_id?}', [ 'uses' => 'Admin\ExpensesController@index', 'as' => 'list_expenses.index'] );
        Route::get('expenses/create/{project_id?}', ['uses' => 'Admin\ExpensesController@create', 'as' => 'expenses.create']);
        
        Route::post('expenses_mass_destroy', ['uses' => 'Admin\ExpensesController@massDestroy', 'as' => 'expenses.mass_destroy']);
        Route::resource('expense_categories', 'Admin\ExpenseCategoriesController');
        Route::get('expense_categories/{id}/{list?}', [ 'uses' => 'Admin\ExpenseCategoriesController@show', 'as' => 'expense_categories.show' ] );
        Route::post('expense_categories_mass_destroy', ['uses' => 'Admin\ExpenseCategoriesController@massDestroy', 'as' => 'expense_categories.mass_destroy']);
        Route::resource('income_categories', 'Admin\IncomeCategoriesController');
        Route::get('income_categories/{id}/{list?}', [ 'uses' => 'Admin\IncomeCategoriesController@show', 'as' => 'income_categories.show' ] );
        Route::post('income_categories_mass_destroy', ['uses' => 'Admin\IncomeCategoriesController@massDestroy', 'as' => 'income_categories.mass_destroy']);
        Route::resource('monthly_reports', 'Admin\MonthlyReportsController');
        
        Route::resource('transfers', 'Admin\TransfersController');
        Route::get('list-transfers/{type?}/{type_id?}', [ 'uses' => 'Admin\TransfersController@index', 'as' => 'list_transfers.index'] );
        Route::post('transfers_mass_destroy', ['uses' => 'Admin\TransfersController@massDestroy', 'as' => 'transfers.mass_destroy']);
        Route::post('transfers_restore/{id}', ['uses' => 'Admin\TransfersController@restore', 'as' => 'transfers.restore']);
        Route::delete('transfers_perma_del/{id}', ['uses' => 'Admin\TransfersController@perma_del', 'as' => 'transfers.perma_del']);
        Route::get('transfers/{account_id}/{list?}', [ 'uses' => 'Admin\TransfersController@show', 'as' => 'transfers.show' ] );
            
        Route::resource('invoices', 'Admin\InvoicesController');
        Route::get('list-invoices/{type?}/{type_id?}', [ 'uses' => 'Admin\InvoicesController@index', 'as' => 'list_invoices.index'] );
        Route::post('invoice/send', 'Admin\InvoicesController@invoiceSend');
        Route::post('invoice/save-payment', 'Admin\InvoicesController@savePayment');
        Route::get('invoice/changestatus/{id}/{status}', [ 'uses' => 'Admin\InvoicesController@changeStatus', 'as' => 'invoices.changestatus'] );
        Route::post('invoices_mass_destroy', ['uses' => 'Admin\InvoicesController@massDestroy', 'as' => 'invoices.mass_destroy']);
        Route::post('invoices_restore/{id}', ['uses' => 'Admin\InvoicesController@restore', 'as' => 'invoices.restore']);
        Route::delete('invoices_perma_del/{id}', ['uses' => 'Admin\InvoicesController@perma_del', 'as' => 'invoices.perma_del']);
        Route::post('invoices/mail-invoice', ['uses' => 'Admin\InvoicesController@mailInvoice', 'as' => 'invoices.mail_invoice']);
        Route::get('invoices/preview/{slug}', [ 'uses' => 'Admin\InvoicesController@showPreview', 'as' => 'invoices.preview'] );
        Route::get('invoices/invoicepdf/{slug}/{operation?}', [ 'uses' => 'Admin\InvoicesController@invoicePDF', 'as' => 'invoices.invoicepdf'] );
        Route::get('invoices/duplicate/{slug}', [ 'uses' => 'Admin\InvoicesController@duplicate', 'as' => 'invoices.duplicate'] );
        
        Route::get('invoices/upload/{slug}', [ 'uses' => 'Admin\InvoicesController@uploadDocuments', 'as' => 'invoices.upload'] );
        Route::post('invoices/upload/{slug}', [ 'uses' => 'Admin\InvoicesController@upload', 'as' => 'invoices.process-upload'] );
        Route::post('invoices/signature', [ 'uses' => 'Admin\InvoicesController@signature', 'as' => 'invoice.save-invoice-signature'] );

        Route::post('invoices/refresh-stats', [ 'uses' => 'Admin\InvoicesController@refreshStats', 'as' => 'invoices.refresh-stats'] );

        //Credit Note        
        Route::resource('credit_notes', 'Admin\CreditNotesController');
        Route::get('list-credit_notes/{type?}/{type_id?}', [ 'uses' => 'Admin\CreditNotesController@index', 'as' => 'list_credit_notes.index'] );
        Route::post('credit_note/send', 'Admin\CreditNotesController@invoiceSend');
        Route::post('credit_note/save-payment', 'Admin\CreditNotesController@savePayment');
        Route::get('credit_note/changestatus/{id}/{status}', [ 'uses' => 'Admin\CreditNotesController@changeStatus', 'as' => 'credit_notes.changestatus'] );
        Route::post('credit_notes_mass_destroy', ['uses' => 'Admin\CreditNotesController@massDestroy', 'as' => 'credit_notes.mass_destroy']);
        Route::post('credit_notes_restore/{id}', ['uses' => 'Admin\CreditNotesController@restore', 'as' => 'credit_notes.restore']);
        Route::delete('credit_notes_perma_del/{id}', ['uses' => 'Admin\CreditNotesController@perma_del', 'as' => 'credit_notes.perma_del']);
        Route::post('credit_notes/mail-invoice', ['uses' => 'Admin\CreditNotesController@mailInvoice', 'as' => 'credit_notes.mail_invoice']);
        Route::get('credit_notes/preview/{slug}', [ 'uses' => 'Admin\CreditNotesController@showPreview', 'as' => 'credit_notes.preview'] );
        Route::get('credit_notes/invoicepdf/{slug}/{operation?}', [ 'uses' => 'Admin\CreditNotesController@invoicePDF', 'as' => 'credit_notes.invoicepdf'] );
        Route::get('credit_notes/duplicate/{slug}', [ 'uses' => 'Admin\CreditNotesController@duplicate', 'as' => 'credit_notes.duplicate'] );
        
        Route::get('credit_notes/upload/{slug}', [ 'uses' => 'Admin\CreditNotesController@uploadDocuments', 'as' => 'credit_notes.upload'] );
        Route::post('credit_notes/upload/{slug}', [ 'uses' => 'Admin\CreditNotesController@upload', 'as' => 'credit_notes.process-upload'] );
        Route::post('credit_notes/signature', [ 'uses' => 'Admin\CreditNotesController@signature', 'as' => 'credit_note.save-invoice-signature'] );
        Route::post('credit_notes/refresh-stats', [ 'uses' => 'Admin\CreditNotesController@refreshStats', 'as' => 'credit_notes.refresh-stats'] );

        Route::resource('credit_notepayments', 'Admin\CreditNotePaymentsController');
        Route::post('credit_note/apply', [ 'uses' => 'Admin\CreditNotesController@applytoInvoice', 'as' => 'credit_notes.apply-to-invoice'] );
        Route::post('credit_notes_ajax/apply', [ 'uses' => 'Admin\CreditNotesController@applytoInvoiceAjax', 'as' => 'credit_notes.applytoinvoice'] );



        // Customer Invoice Payment.
        Route::get('payment/paynow/{module}/{id}/{paymethod}', [ 'uses' => 'Admin\PaymentsController@payNow', 'as' => 'payment.paynow' ]);
        Route::post('payment/process-payment/{slug}/{module}', ['uses' => 'Admin\PaymentsController@processPayment', 'as' => 'payment.process-payment']);
        Route::get('payment/payment-payu/{slug}/{module}', ['uses' => 'Admin\PaymentsController@processPayu', 'as' => 'payment.process-payu']);

        Route::get('payment/payment-failed/{slug}/{module}', ['uses' => 'Admin\PaymentsController@paymentFailed', 'as' => 'payment.payment-failed']);
        Route::get('payment/payment-cancelled/{slug}/{module}', ['uses' => 'Admin\PaymentsController@paymentCancelled', 'as' => 'payment.payment-cancelled']);
        Route::get('payment/payment-success/{slug}/{module}', ['uses' => 'Admin\PaymentsController@paymentSuccess', 'as' => 'payment.payment-success']);
        

        Route::resource('purchase_orders', 'Admin\PurchaseOrdersController');
        Route::get('list-purchase_orders/{type?}/{type_id?}', [ 'uses' => 'Admin\PurchaseOrdersController@index', 'as' => 'list_purchase_orders.index'] );
        Route::post('purchase_orders_mass_destroy', ['uses' => 'Admin\PurchaseOrdersController@massDestroy', 'as' => 'purchase_orders.mass_destroy']);
        Route::post('purchase_orders_restore/{id}', ['uses' => 'Admin\PurchaseOrdersController@restore', 'as' => 'purchase_orders.restore']);
        Route::delete('purchase_orders_perma_del/{id}', ['uses' => 'Admin\PurchaseOrdersController@perma_del', 'as' => 'purchase_orders.perma_del']);

        Route::post('purchase_orders/send', 'Admin\PurchaseOrdersController@invoiceSend');
        Route::post('purchase_orders/save-payment', 'Admin\PurchaseOrdersController@savePayment');
        Route::get('purchase_orders/changestatus/{id}/{status}', [ 'uses' => 'Admin\PurchaseOrdersController@changeStatus', 'as' => 'purchase_orders.changestatus'] );
        Route::post('purchase_orders/mail-invoice', ['uses' => 'Admin\PurchaseOrdersController@mailInvoice', 'as' => 'purchase_orders.mail_invoice']);
        Route::get('purchase_orders/preview/{slug}', [ 'uses' => 'Admin\PurchaseOrdersController@showPreview', 'as' => 'purchase_orders.preview'] );
        Route::get('purchase_orders/invoicepdf/{slug}/{operation?}', [ 'uses' => 'Admin\PurchaseOrdersController@invoicePDF', 'as' => 'purchase_orders.invoicepdf'] );
        Route::get('purchase_orders/upload/{slug}', [ 'uses' => 'Admin\PurchaseOrdersController@uploadDocuments', 'as' => 'purchase_orders.upload'] );
        Route::post('purchase_orders/upload/{slug}', [ 'uses' => 'Admin\PurchaseOrdersController@upload', 'as' => 'purchase_orders.process-upload'] );
        Route::get('purchase_orders/duplicate/{slug}', [ 'uses' => 'Admin\PurchaseOrdersController@duplicate', 'as' => 'purchase_orders.duplicate'] );
        Route::get('purchase_orders/convert-to-invoice/{slug}', [ 'uses' => 'Admin\PurchaseOrdersController@convertToInvoice', 'as' => 'purchase_orders.convertinvoice'] );

        Route::post('purchase_orders/refresh-stats', [ 'uses' => 'Admin\PurchaseOrdersController@refreshStats', 'as' => 'purchase_orders.refresh-stats'] );

        Route::resource('permissions', 'Admin\PermissionsController');
        Route::post('permissions_mass_destroy', ['uses' => 'Admin\PermissionsController@massDestroy', 'as' => 'permissions.mass_destroy']);

        Route::resource('roles', 'Admin\RolesController');
        Route::get('roles/{id}/{list?}', [ 'uses' => 'Admin\RolesController@show', 'as' => 'roles.show' ] );
        Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);

        Route::resource('users', 'Admin\UsersController');
        Route::get('users/create/{contact_id}', [ 'uses' => 'Admin\UsersController@create', 'as' => 'users.create' ]);
        Route::post('users/store/{contact_id}', [ 'uses' => 'Admin\UsersController@store', 'as' => 'users.store' ]);

        Route::get('users/{user_id}/{list?}', [ 'uses' => 'Admin\UsersController@show', 'as' => 'users.show' ] );
        Route::get('users/create/{contact_id}', ['uses' => 'Admin\UsersController@create', 'as' => 'users.create']);
        Route::get('list-users/{type?}/{type_id?}', [ 'uses' => 'Admin\UsersController@index', 'as' => 'list_users.index' ] );
        Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
        Route::post('users/getuser/byid', ['uses' => 'Admin\UsersController@getUser', 'as' => 'users.getuserbyid']);
        Route::get('user/changestatus/{id}', [ 'uses' =>  'Admin\UsersController@changeStatus', 'as' => 'users.changestatus' ] );
        Route::get('list-users/{type?}/{type_id?}', [ 'uses' => 'Admin\UsersController@index', 'as' => 'list_users.index' ] );

        Route::get('internal_notifications/read', 'Admin\InternalNotificationsController@read');
        Route::resource('internal_notifications', 'Admin\InternalNotificationsController');
        Route::post('internal_notifications_mass_destroy', ['uses' => 'Admin\InternalNotificationsController@massDestroy', 'as' => 'internal_notifications.mass_destroy']);
        Route::resource('user_actions', 'Admin\UserActionsController');
        Route::get('list-user_actions/{type?}/{type_id?}', ['uses' => 'Admin\UserActionsController@index', 'as' => 'list_user_actions.index']);

        Route::post('user_actions_mass_destroy', ['uses' => 'Admin\UserActionsController@massDestroy', 'as' => 'user_actions.mass_destroy']);
        Route::resource('departments', 'Admin\DepartmentsController');
        Route::get('departments/{id}/{list?}', [ 'uses' => 'Admin\DepartmentsController@show', 'as' => 'departments.show' ] );
        Route::post('departments_mass_destroy', ['uses' => 'Admin\DepartmentsController@massDestroy', 'as' => 'departments.mass_destroy']);
        Route::post('departments_restore/{id}', ['uses' => 'Admin\DepartmentsController@restore', 'as' => 'departments.restore']);
        Route::delete('departments_perma_del/{id}', ['uses' => 'Admin\DepartmentsController@perma_del', 'as' => 'departments.perma_del']);
        Route::resource('contact_companies', 'Admin\ContactCompaniesController');
        Route::get('contact_companies/{id}/{list?}', [ 'uses' => 'Admin\ContactCompaniesController@show', 'as' => 'contact_companies.show' ] );
        Route::get('list-companies/{type?}/{type_id?}', [ 'uses' => 'Admin\ContactCompaniesController@index', 'as' => 'list_companies.index' ] );
        Route::post('contact_companies_mass_destroy', ['uses' => 'Admin\ContactCompaniesController@massDestroy', 'as' => 'contact_companies.mass_destroy']);
        
        Route::resource('contacts', 'Admin\ContactsController');
        Route::get('contacts/create/{type?}', [ 'uses' => 'Admin\ContactsController@create', 'as' => 'contacts.create' ] );
        Route::post('contacts/store/{type?}', [ 'uses' => 'Admin\ContactsController@store', 'as' => 'contacts.store' ] );
        
        Route::get('list-contacts/{type?}/{type_id?}', [ 'uses' => 'Admin\ContactsController@index', 'as' => 'list_contacts.index' ] );

        Route::get('contacts/{contact_id}/{list?}', [ 'uses' => 'Admin\ContactsController@show', 'as' => 'contacts.show' ] );
        
        Route::post('contacts_mass_destroy', ['uses' => 'Admin\ContactsController@massDestroy', 'as' => 'contacts.mass_destroy']);
        Route::post('contacts_restore/{id}', ['uses' => 'Admin\ContactsController@restore', 'as' => 'contacts.restore']);
        Route::delete('contacts_perma_del/{id}', ['uses' => 'Admin\ContactsController@perma_del', 'as' => 'contacts.perma_del']);

        Route::get('contacts-info/{id}', ['uses' => 'Admin\ContactsController@destroyInfo', 'as' => 'contacts.info']);
        Route::match(['get', 'post'], 'del-permanent/{id}', ['uses' => 'Admin\ContactsController@del_permanent', 'as' => 'contacts.del_permanent']);

        Route::post('contacts_mass_destroy', ['uses' => 'Admin\ContactsController@massDestroy', 'as' => 'contacts.mass_destroy']);
        Route::get('lead_convert/{contact_id}/{contact_type_id}', [ 'uses' => 'Admin\ContactsController@leadConvert', 'as' => 'contacts.lead_convert' ] );

        Route::get('profile/edit', [ 'uses' => 'Admin\ContactsController@profileEdit', 'as' => 'contacts.profile.edit' ]);
        Route::post('profile/edit', [ 'uses' => 'Admin\ContactsController@profileUpdate', 'as' => 'contacts.profile.update' ]);

        Route::get('delivery-address/edit/{id?}', [ 'uses' => 'Admin\ContactsController@deliveryAddressEdit', 'as' => 'contacts.delivery-address.edit' ]);
        Route::post('delivery-address/edit/{id?}', [ 'uses' => 'Admin\ContactsController@deliveryAddressUpdate', 'as' => 'contacts.delivery-address.update' ]);

        Route::get('shipping-address/edit/{id?}', [ 'uses' => 'Admin\ContactsController@shippingAddressEdit', 'as' => 'contacts.shipping-address.edit' ]);
        Route::post('shipping-address/edit/{id?}', [ 'uses' => 'Admin\ContactsController@shippingAddressUpdate', 'as' => 'contacts.shipping-address.update' ]);
        Route::post('contacts/send-email', [ 'uses' => 'Admin\ContactsController@sendEmail', 'as' => 'contacts.send-email' ]);
        Route::get('contact/mailchimp-email-campaigns/{list_id?}', [ 'uses' => 'Admin\ContactsController@mailchimpEmailCampaigns', 'as' => 'contacts.mailchimp-email-campaigns' ]);
        Route::get('contact/run-mailchimp-email-campaigns/{list_id}', [ 'uses' => 'Admin\ContactsController@RunMailchimpEmailCampaigns', 'as' => 'contacts.run-mailchimp-email-campaigns' ]);
        Route::post('contact/run-mailchimp-email-campaigns/{list_id}', [ 'uses' => 'Admin\ContactsController@RunMailchimpEmailCampaigns', 'as' => 'contacts.run-mailchimp-email-campaigns-post' ]);
        
        Route::get('contact/send-bulk-emails/{contact_type?}', [ 'uses' => 'Admin\ContactsController@sendBulkEmailQueue', 'as' => 'contacts.send-bulk-emails' ]);
        Route::post('contact/send-bulk-emails/{contact_type?}', [ 'uses' => 'Admin\ContactsController@runBulkEmailQueue', 'as' => 'contacts.send-bulk-emails-post' ]);
        
        Route::resource('customers', 'Admin\CustomersController');
        Route::post('customers_mass_destroy', ['uses' => 'Admin\CustomersController@massDestroy', 'as' => 'customers.mass_destroy']);

        Route::resource('suppliers', 'Admin\SuppliersController');
        Route::post('supliers_mass_destroy', ['uses' => 'Admin\SuppliersController@massDestroy', 'as' => 'suppliers.mass_destroy']);

        Route::resource('leads', 'Admin\LeadsController');
        Route::post('leads_mass_destroy', ['uses' => 'Admin\LeadsController@massDestroy', 'as' => 'leads.mass_destroy']);
        
        Route::resource('clients', 'Admin\ClientsController');
        Route::post('clients_mass_destroy', ['uses' => 'Admin\ClientsController@massDestroy', 'as' => 'clients.mass_destroy']);

        /*Employee controller*/
        Route::resource('employees', 'Admin\EmployeesController');
        Route::post('employees_mass_destroy', ['uses' => 'Admin\EmployeesController@massDestroy', 'as' => 'employees.mass_destroy']);
        /*End employee controller*/

        Route::resource('saleagents', 'Admin\SaleAgentsController');
        Route::post('saleagents_mass_destroy', ['uses' => 'Admin\SaleAgentsController@massDestroy', 'as' => 'saleagents.mass_destroy']);

        Route::resource('contact_groups', 'Admin\ContactGroupsController');
        Route::post('contact_groups_mass_destroy', ['uses' => 'Admin\ContactGroupsController@massDestroy', 'as' => 'contact_groups.mass_destroy']);
        Route::post('contact_groups_restore/{id}', ['uses' => 'Admin\ContactGroupsController@restore', 'as' => 'contact_groups.restore']);
        Route::delete('contact_groups_perma_del/{id}', ['uses' => 'Admin\ContactGroupsController@perma_del', 'as' => 'contact_groups.perma_del']);
        Route::get('contact_groups/{account_id}/{list?}', [ 'uses' => 'Admin\ContactGroupsController@show', 'as' => 'contact_groups.show' ] );
        
         Route::post('customers/refresh-stats', [ 'uses' => 'Admin\CustomersController@refreshStats', 'as' => 'customers.refresh-stats'] );
         Route::post('suppliers/refresh-stats', [ 'uses' => 'Admin\SuppliersController@refreshStats', 'as' => 'suppliers.refresh-stats'] );

        
        Route::resource('contact_types', 'Admin\ContactTypesController');
        Route::get('contact_types/{id}/{list?}', [ 'uses' => 'Admin\ContactTypesController@show', 'as' => 'contact_types.show' ] );
        Route::post('contact_types_mass_destroy', ['uses' => 'Admin\ContactTypesController@massDestroy', 'as' => 'contact_types.mass_destroy']);
        Route::post('contact_types_restore/{id}', ['uses' => 'Admin\ContactTypesController@restore', 'as' => 'contact_types.restore']);
        Route::delete('contact_types_perma_del/{id}', ['uses' => 'Admin\ContactTypesController@perma_del', 'as' => 'contact_types.perma_del']);

        Route::resource('contact_notes', 'Admin\ContactNotesController');
        Route::get('customer-contact_notes/{type?}/{contact_id?}', [ 'uses' => 'Admin\ContactNotesController@index', 'as' => 'customer_contact_notes.index' ]);
        Route::post('contact_notes_mass_destroy', ['uses' => 'Admin\ContactNotesController@massDestroy', 'as' => 'contact_notes.mass_destroy']);
        Route::post('contact_notes_restore/{id}', ['uses' => 'Admin\ContactNotesController@restore', 'as' => 'contact_notes.restore']);
        Route::delete('contact_notes_perma_del/{id}', ['uses' => 'Admin\ContactNotesController@perma_del', 'as' => 'contact_notes.perma_del']);
        
        Route::resource('contact_documents', 'Admin\ContactDocumentsController');
        Route::get('customer-contact_documents/{contact_id}', [ 'uses' => 'Admin\ContactDocumentsController@index', 'as' => 'customer_contact_documents.index' ]);
        Route::post('contact_documents_mass_destroy', ['uses' => 'Admin\ContactDocumentsController@massDestroy', 'as' => 'contact_documents.mass_destroy']);
        Route::post('contact_documents_restore/{id}', ['uses' => 'Admin\ContactDocumentsController@restore', 'as' => 'contact_documents.restore']);
        Route::delete('contact_documents_perma_del/{id}', ['uses' => 'Admin\ContactDocumentsController@perma_del', 'as' => 'contact_documents.perma_del']);
        Route::resource('project_statuses', 'Admin\ProjectStatusesController');
        Route::get('project_statuses/{id}/{list?}', [ 'uses' => 'Admin\ProjectStatusesController@show', 'as' => 'project_statuses.show' ] );
        Route::post('project_statuses_mass_destroy', ['uses' => 'Admin\ProjectStatusesController@massDestroy', 'as' => 'project_statuses.mass_destroy']);
        Route::post('project_statuses_restore/{id}', ['uses' => 'Admin\ProjectStatusesController@restore', 'as' => 'project_statuses.restore']);
        Route::delete('project_statuses_perma_del/{id}', ['uses' => 'Admin\ProjectStatusesController@perma_del', 'as' => 'project_statuses.perma_del']);
        Route::resource('client_projects', 'Admin\ClientProjectsController');
        Route::get('list-client-projects/{type?}/{type_id?}', [ 'uses' => 'Admin\ClientProjectsController@index', 'as' => 'list_client_projects.index'] );
        

        Route::post('client_projects_mass_destroy', ['uses' => 'Admin\ClientProjectsController@massDestroy', 'as' => 'client_projects.mass_destroy']);
        Route::post('client_projects_restore/{id}', ['uses' => 'Admin\ClientProjectsController@restore', 'as' => 'client_projects.restore']);
        Route::delete('client_projects_perma_del/{id}', ['uses' => 'Admin\ClientProjectsController@perma_del', 'as' => 'client_projects.perma_del']);
        Route::get('client-projects/duplicate/{id}', ['uses' => 'Admin\ClientProjectsController@duplicate', 'as' => 'client_projects.duplicate']);

        Route::get('client-projects/gantt-chart/{id}', ['uses' => 'Admin\ClientProjectsController@ganttChart', 'as' => 'client_projects.gantt-chart']);
        
        Route::get('client_projects/invoices/{project_id}', ['uses' => 'Admin\ClientProjectsController@invoices', 'as' => 'client_projects.invoices'] );
        Route::get('client_projects/invoice-project/{project_id}', ['uses' => 'Admin\ClientProjectsController@invoiceProject', 'as' => 'client_projects.invoice-project'] );
        
        Route::get('client_projects/invoice-project-edit/{project_id}/{id}', ['uses' => 'Admin\ClientProjectsController@invoiceProjectEdit', 'as' => 'client_projects.invoice-project-edit'] );
        
        Route::get('client_projects/invoice-project-preview/{project_id}', ['uses' => 'Admin\ClientProjectsController@invoiceProject', 'as' => 'client_projects.invoice-project-preview'] );

        Route::post('client_projects/invoice-project-preview/{project_id}', ['uses' => 'Admin\ClientProjectsController@invoiceProjectPreview', 'as' => 'client_projects.invoice-project-preview'] );
        Route::post('client_projects/invoice-project-store/{project_id}/{id?}', ['uses' => 'Admin\ClientProjectsController@invoiceProjectStore', 'as' => 'client_projects.invoice-project-store'] );

        Route::post('client_projects/refresh-stats', [ 'uses' => 'Admin\ClientProjectsController@refreshStats', 'as' => 'client_projects.refresh-stats'] );
        
        // Invoice Expenses.
        Route::get('client_projects/expenses/{project_id}', ['uses' => 'Admin\ClientProjectsController@expenses', 'as' => 'client_projects.expenses'] );


        Route::resource('project_billing_types', 'Admin\ProjectBillingTypesController');
        Route::get('project_billing_types/{id}/{list?}', [ 'uses' => 'Admin\ProjectBillingTypesController@show', 'as' => 'project_billing_types.show' ] );
        Route::post('project_billing_types_mass_destroy', ['uses' => 'Admin\ProjectBillingTypesController@massDestroy', 'as' => 'project_billing_types.mass_destroy']);
        Route::post('project_billing_types_restore/{id}', ['uses' => 'Admin\ProjectBillingTypesController@restore', 'as' => 'project_billing_types.restore']);
        Route::delete('project_billing_types_perma_del/{id}', ['uses' => 'Admin\ProjectBillingTypesController@perma_del', 'as' => 'project_billing_types.perma_del']);
        
        // Time entries.
        Route::get('time_entries/{project_id?}', ['uses' => 'Admin\TimeEntriesController@index', 'as' => 'time_entries.index'] );
        Route::get('time_entries/{project_id?}/create', ['uses' => 'Admin\TimeEntriesController@create', 'as' => 'time_entries.create'] );
        Route::post('time_entries/{project_id?}/create', ['uses' => 'Admin\TimeEntriesController@store', 'as' => 'time_entries.store'] ); 
        Route::post('time_entries_mass_destroy/{project_id?}', ['uses' => 'Admin\TimeEntriesController@massDestroy', 'as' => 'time_entries.mass_destroy']);
        Route::get('time_entries/{project_id}/edit/{id}', ['uses' => 'Admin\TimeEntriesController@edit', 'as' => 'time_entries.edit'] );
        Route::put('time_entries/{project_id}/edit/{id}', ['uses' => 'Admin\TimeEntriesController@update', 'as' => 'time_entries.update'] );
        Route::get('time_entries/{project_id}/show/{id}', ['uses' => 'Admin\TimeEntriesController@show', 'as' => 'time_entries.show'] );
        Route::delete('time_entries/{project_id?}/{id}', ['uses' => 'Admin\TimeEntriesController@destroy', 'as' => 'time_entries.destroy']);
        Route::post('time_entries_restore/{project_id?}/{id}', ['uses' => 'Admin\TimeEntriesController@restore', 'as' => 'time_entries.restore']);
        Route::delete('time_entries_perma_del/{project_id?}/{id}', ['uses' => 'Admin\TimeEntriesController@perma_del', 'as' => 'time_entries.perma_del']);

        // Project Tickets.
        Route::get('project_tickets/{project_id?}', ['uses' => 'Admin\ProjectTicketsController@index', 'as' => 'project_tickets.index'] );
        Route::get('project_tickets/{project_id?}/create', ['uses' => 'Admin\ProjectTicketsController@create', 'as' => 'project_tickets.create'] );
        Route::post('project_tickets/{project_id?}/create', ['uses' => 'Admin\ProjectTicketsController@store', 'as' => 'project_tickets.store'] ); 
        Route::post('project_tickets/{project_id?}', ['uses' => 'Admin\ProjectTicketsController@massDestroy', 'as' => 'project_tickets.mass_destroy']);
        Route::get('project_tickets/{project_id}/edit/{id}', ['uses' => 'Admin\ProjectTicketsController@edit', 'as' => 'project_tickets.edit'] );
        Route::put('project_tickets/{project_id}/edit/{id}', ['uses' => 'Admin\ProjectTicketsController@update', 'as' => 'project_tickets.update'] );
        Route::get('project_tickets/{project_id}/show/{id}', ['uses' => 'Admin\ProjectTicketsController@show', 'as' => 'project_tickets.show'] );
        Route::delete('project_tickets/{project_id?}/{id}', ['uses' => 'Admin\ProjectTicketsController@destroy', 'as' => 'project_tickets.destroy']);
        Route::post('project_tickets/{project_id?}/{id}', ['uses' => 'Admin\ProjectTicketsController@restore', 'as' => 'project_tickets.restore']);
        Route::delete('project_tickets_perma_del/{project_id?}/{id}', ['uses' => 'Admin\ProjectTicketsController@perma_del', 'as' => 'project_tickets.perma_del']);

        // Project Discussions.
        Route::get('project_discussions/{project_id?}', ['uses' => 'Admin\ProjectDiscussionController@index', 'as' => 'project_discussions.index'] );
        Route::get('project_discussions/{project_id?}/create', ['uses' => 'Admin\ProjectDiscussionController@create', 'as' => 'project_discussions.create'] );
        Route::post('project_discussions/{project_id?}/create', ['uses' => 'Admin\ProjectDiscussionController@store', 'as' => 'project_discussions.store'] ); 
        Route::post('project_discussions/{project_id?}', ['uses' => 'Admin\ProjectDiscussionController@massDestroy', 'as' => 'project_discussions.mass_destroy']);
        Route::get('project_discussions/{project_id}/edit/{id}', ['uses' => 'Admin\ProjectDiscussionController@edit', 'as' => 'project_discussions.edit'] );
        Route::put('project_discussions/{project_id}/edit/{id}', ['uses' => 'Admin\ProjectDiscussionController@update', 'as' => 'project_discussions.update'] );
        Route::get('project_discussions/{project_id}/show/{id}', ['uses' => 'Admin\ProjectDiscussionController@show', 'as' => 'project_discussions.show'] );
        Route::delete('project_discussions/{project_id?}/{id}', ['uses' => 'Admin\ProjectDiscussionController@destroy', 'as' => 'project_discussions.destroy']);
        Route::post('project_discussions/{project_id?}/{id}', ['uses' => 'Admin\ProjectDiscussionController@restore', 'as' => 'project_discussions.restore']);
        Route::delete('project_discussions_perma_del/{project_id?}/{id}', ['uses' => 'Admin\ProjectDiscussionController@perma_del', 'as' => 'project_discussions.perma_del']);
        
        Route::get('project_discussions_comments/{project_id?}/{discussion_id?}/{operation?}/{id?}', ['uses' => 'Admin\ProjectDiscussionController@projectDiscussionComments', 'as' => 'project_discussions.comments'] );
        Route::post('project_discussions_comments_store/{project_id}/{discussion_id}/{operation?}/{id?}', ['uses' => 'Admin\ProjectDiscussionController@commentsStore', 'as' => 'project_discussions.comments-store']);
        Route::delete('project_discussions_comments_perma_del/{project_id?}/{id}', ['uses' => 'Admin\ProjectDiscussionController@commentDelete', 'as' => 'project_comment.destroy']);
        
        // Mile stones.
        Route::get('mile_stones/{project_id?}', ['uses' => 'Admin\MileStonesController@index', 'as' => 'mile_stones.index'] );
        Route::get('mile_stones_tasks/{project_id?}/{mile_stone_id?}', ['uses' => 'Admin\MileStonesController@mileStoneTasks', 'as' => 'mile_stones.tasks'] );
        Route::get('mile_stones/{project_id?}/create', ['uses' => 'Admin\MileStonesController@create', 'as' => 'mile_stones.create'] );
        Route::post('mile_stones/{project_id?}/create', ['uses' => 'Admin\MileStonesController@store', 'as' => 'mile_stones.store'] ); 

        Route::post('mile_stones_mass_destroy/{project_id?}', ['uses' => 'Admin\MileStonesController@massDestroy', 'as' => 'mile_stones.mass_destroy']);

        Route::get('mile_stones/{project_id}/edit/{id}', ['uses' => 'Admin\MileStonesController@edit', 'as' => 'mile_stones.edit'] );
        Route::put('mile_stones/{project_id}/edit/{id}', ['uses' => 'Admin\MileStonesController@update', 'as' => 'mile_stones.update'] );

        Route::get('mile_stones/{project_id}/show/{id}', ['uses' => 'Admin\MileStonesController@show', 'as' => 'mile_stones.show'] );
        Route::delete('mile_stones/{project_id?}/{id}', ['uses' => 'Admin\MileStonesController@destroy', 'as' => 'mile_stones.destroy']);
        Route::post('mile_stones_restore/{project_id?}/{id}', ['uses' => 'Admin\MileStonesController@restore', 'as' => 'mile_stones.restore']);
        Route::delete('mile_stones_perma_del/{project_id?}/{id}', ['uses' => 'Admin\MileStonesController@perma_del', 'as' => 'mile_stones.perma_del']);

        // Project tabs.
        Route::resource('project_tabs', 'Admin\ProjectTabsController');
        Route::post('project_tabs_mass_destroy', ['uses' => 'Admin\ProjectTabsController@massDestroy', 'as' => 'project_tabs.mass_destroy']);
        Route::post('project_tabs_restore/{id}', ['uses' => 'Admin\ProjectTabsController@restore', 'as' => 'project_tabs.restore']);
        Route::delete('project_tabs_perma_del/{id}', ['uses' => 'Admin\ProjectTabsController@perma_del', 'as' => 'project_tabs.perma_del']);

        // Project tasks
        Route::get('project_tasks/{project_id?}', ['uses' => 'Admin\ProjectTasksController@index', 'as' => 'project_tasks.index'] );
        Route::get('project_tasks/{project_id?}/create', ['uses' => 'Admin\ProjectTasksController@create', 'as' => 'project_tasks.create'] );
        Route::post('project_tasks/{project_id?}/create', ['uses' => 'Admin\ProjectTasksController@store', 'as' => 'project_tasks.store'] );
        Route::post('project_tasks_mass_destroy/{project_id?}', ['uses' => 'Admin\ProjectTasksController@massDestroy', 'as' => 'project_tasks.mass_destroy']);
        Route::get('project_tasks/{project_id}/edit/{id}', ['uses' => 'Admin\ProjectTasksController@edit', 'as' => 'project_tasks.edit'] );
        Route::put('project_tasks/{project_id}/edit/{id}', ['uses' => 'Admin\ProjectTasksController@update', 'as' => 'project_tasks.update'] );
        Route::get('project_tasks/{project_id}/show/{id}', ['uses' => 'Admin\ProjectTasksController@show', 'as' => 'project_tasks.show'] );
        Route::delete('project_tasks/{project_id?}/{id}', ['uses' => 'Admin\ProjectTasksController@destroy', 'as' => 'project_tasks.destroy']);
        Route::post('project_tasks_restore/{project_id?}/{id}', ['uses' => 'Admin\ProjectTasksController@restore', 'as' => 'project_tasks.restore']);
        Route::delete('project_tasks_perma_del/{project_id?}/{id}', ['uses' => 'Admin\ProjectTasksController@perma_del', 'as' => 'project_tasks.perma_del']);
        Route::get('project_tasks/start-timer/{id}', ['uses' => 'Admin\ProjectTasksController@startTimer', 'as' => 'project_tasks.start-timer'] );
        Route::get('project_tasks/stop-timer/{id}/{timer_id?}', ['uses' => 'Admin\ProjectTasksController@stopTimer', 'as' => 'project_tasks.stop-timer'] );

        
        Route::resource('tasks', 'Admin\TasksController');
        Route::get('list-tasks/{type?}/{type_id?}', [ 'uses' => 'Admin\TasksController@index', 'as' => 'list_tasks.index' ] );
        Route::post('tasks_mass_destroy', ['uses' => 'Admin\TasksController@massDestroy', 'as' => 'tasks.mass_destroy']);
        Route::resource('task_statuses', 'Admin\TaskStatusesController');
        Route::get('task_statuses/{id}/{list?}', [ 'uses' => 'Admin\TaskStatusesController@show', 'as' => 'task_statuses.show' ] );
        Route::post('task_statuses_mass_destroy', ['uses' => 'Admin\TaskStatusesController@massDestroy', 'as' => 'task_statuses.mass_destroy']);
        Route::resource('task_tags', 'Admin\TaskTagsController');
        Route::post('task_tags_mass_destroy', ['uses' => 'Admin\TaskTagsController@massDestroy', 'as' => 'task_tags.mass_destroy']);
        Route::resource('task_calendars', 'Admin\TaskCalendarsController');
        Route::post('tasks/calendar/addtask', ['uses' => 'Admin\TaskCalendarsController@addTask', 'as' => 'tasks.calendar.addtask']);
        Route::post('tasks/calendar/updatetask', ['uses' => 'Admin\TaskCalendarsController@addTask', 'as' => 'tasks.calendar.updatetask']);
        Route::post('calendartasks/calendar/deletetask', ['uses' => 'Admin\TaskCalendarsController@deleteTask', 'as' => 'calendartasks.calendar.deletetask']);
        Route::get('calendartasks/tasksstatuses', ['uses' => 'Admin\TaskCalendarsController@tasksStatus', 'as' => 'calendartasks.calendar.taskstatus']);
        Route::post('calendartasks/calendar/updatetaskstatus', ['uses' => 'Admin\TaskCalendarsController@updateTaskStatus', 'as' => 'calendartasks.calendar.updatetaskstatus']);


        Route::resource('assets', 'Admin\AssetsController');
        Route::get('assets/{id}/{list?}', [ 'uses' => 'Admin\AssetsController@show', 'as' => 'assets.show' ] );
        Route::post('assets_mass_destroy', ['uses' => 'Admin\AssetsController@massDestroy', 'as' => 'assets.mass_destroy']);
        Route::resource('assets_categories', 'Admin\AssetsCategoriesController');
        Route::get('assets_categories/{id}/{list?}', [ 'uses' => 'Admin\AssetsCategoriesController@show', 'as' => 'assets_categories.show' ] );
        Route::post('assets_categories_mass_destroy', ['uses' => 'Admin\AssetsCategoriesController@massDestroy', 'as' => 'assets_categories.mass_destroy']);
        Route::resource('assets_locations', 'Admin\AssetsLocationsController');
        Route::get('assets_locations/{id}/{list?}', [ 'uses' => 'Admin\AssetsLocationsController@show', 'as' => 'assets_locations.show' ] );
        Route::post('assets_locations_mass_destroy', ['uses' => 'Admin\AssetsLocationsController@massDestroy', 'as' => 'assets_locations.mass_destroy']);
        Route::resource('assets_statuses', 'Admin\AssetsStatusesController');
        Route::get('assets_statuses/{id}/{list?}', [ 'uses' => 'Admin\AssetsStatusesController@show', 'as' => 'assets_statuses.show' ] );
        Route::post('assets_statuses_mass_destroy', ['uses' => 'Admin\AssetsStatusesController@massDestroy', 'as' => 'assets_statuses.mass_destroy']);
        Route::resource('assets_histories', 'Admin\AssetsHistoriesController');
        
        Route::resource('faq_categories', 'Admin\FaqCategoriesController');
        Route::get('faq_categories/{id}/{list?}', [ 'uses' => 'Admin\FaqCategoriesController@show', 'as' => 'faq_categories.show' ] );
        Route::post('faq_categories_mass_destroy', ['uses' => 'Admin\FaqCategoriesController@massDestroy', 'as' => 'faq_categories.mass_destroy']);
        Route::resource('faq_questions', 'Admin\FaqQuestionsController');
        Route::post('faq_questions_mass_destroy', ['uses' => 'Admin\FaqQuestionsController@massDestroy', 'as' => 'faq_questions.mass_destroy']);
        Route::resource('content_categories', 'Admin\ContentCategoriesController');
        Route::get('content_categories/{id}/{list?}', [ 'uses' => 'Admin\ContentCategoriesController@show', 'as' => 'content_categories.show' ] );
        Route::post('content_categories_mass_destroy', ['uses' => 'Admin\ContentCategoriesController@massDestroy', 'as' => 'content_categories.mass_destroy']);
        Route::resource('content_tags', 'Admin\ContentTagsController');
        Route::get('content_tags/{id}/{list?}', [ 'uses' => 'Admin\ContentTagsController@show', 'as' => 'content_tags.show' ] );
        Route::post('content_tags_mass_destroy', ['uses' => 'Admin\ContentTagsController@massDestroy', 'as' => 'content_tags.mass_destroy']);
        
        Route::resource('content_pages', 'Admin\ContentPagesController');
        Route::get('list_content_pages/{type?}/{type_id?}', [ 'uses' => 'Admin\ContentPagesController@index', 'as' => 'list_content_pages.index'] );
        Route::post('content_pages_mass_destroy', ['uses' => 'Admin\ContentPagesController@massDestroy', 'as' => 'content_pages.mass_destroy']);
        Route::get('page/{type}/{catid?}', ['uses' => 'Admin\ContentPagesController@index', 'as' => 'pages.search']);
        
        // Project files.
        Route::get('project/files/{id}', [ 'uses' => 'Admin\ClientProjectsController@uploadDocuments', 'as' => 'project_files.upload'] );
        Route::post('project/files/{id}', [ 'uses' => 'Admin\ClientProjectsController@upload', 'as' => 'project_files.process-upload'] );
        
        // Project notes.
        Route::get('project/notes/{id}', [ 'uses' => 'Admin\ClientProjectsController@uploadNotes', 'as' => 'project_files.note'] );
        Route::post('project/notes/{id}', [ 'uses' => 'Admin\ClientProjectsController@uploadNote', 'as' => 'project_files.process-note'] );
        

        Route::resource('product_categories', 'Admin\ProductCategoriesController');
        Route::get('product_categories/{id}/{list?}', [ 'uses' => 'Admin\ProductCategoriesController@show', 'as' => 'product_categories.show' ] );
        Route::post('product_categories_mass_destroy', ['uses' => 'Admin\ProductCategoriesController@massDestroy', 'as' => 'product_categories.mass_destroy']);

        Route::resource('products', 'Admin\ProductsController');
        Route::get('list-products/{type?}/{type_id?}', [ 'uses' => 'Admin\ProductsController@index', 'as' => 'list_products.index'] );
        Route::post('products_mass_destroy', ['uses' => 'Admin\ProductsController@massDestroy', 'as' => 'products.mass_destroy']);
        Route::post('products/fill/prices', ['uses' => 'Admin\ProductsController@fillPrices', 'as' => 'products.fillprices']);

        Route::post('products_restore/{id}', ['uses' => 'Admin\ProductsController@restore', 'as' => 'products.restore']);
        Route::delete('products_perma_del/{id}', ['uses' => 'Admin\ProductsController@perma_del', 'as' => 'products.perma_del']);


        
        Route::resource('products_transfers', 'Admin\ProductsTransfersController');
        Route::resource('products_returns', 'Admin\ProductsReturnsController');
        Route::post('products_returns_mass_destroy', ['uses' => 'Admin\ProductsReturnsController@massDestroy', 'as' => 'products_returns.mass_destroy']);
        Route::post('products_returns_restore/{id}', ['uses' => 'Admin\ProductsReturnsController@restore', 'as' => 'products_returns.restore']);
        Route::delete('products_returns_perma_del/{id}', ['uses' => 'Admin\ProductsReturnsController@perma_del', 'as' => 'products_returns.perma_del']);
        Route::resource('brands', 'Admin\BrandsController');
        Route::get('list-brands/{type?}/{type_id?}', [ 'uses' => 'Admin\BrandsController@index', 'as' => 'list_brands.index'] );
         Route::get('brands/{id}/{list?}', [ 'uses' => 'Admin\BrandsController@show', 'as' => 'brands.show' ] );
        Route::post('brands_mass_destroy', ['uses' => 'Admin\BrandsController@massDestroy', 'as' => 'brands.mass_destroy']);
        Route::post('brands_restore/{id}', ['uses' => 'Admin\BrandsController@restore', 'as' => 'brands.restore']);
        Route::delete('brands_perma_del/{id}', ['uses' => 'Admin\BrandsController@perma_del', 'as' => 'brands.perma_del']);
        Route::resource('master_settings', 'Admin\MasterSettingsController');
        Route::post('master_settings_mass_destroy', ['uses' => 'Admin\MasterSettingsController@massDestroy', 'as' => 'master_settings.mass_destroy']);
        Route::post('master_settings_restore/{id}', ['uses' => 'Admin\MasterSettingsController@restore', 'as' => 'master_settings.restore']);
        Route::delete('master_settings_perma_del/{id}', ['uses' => 'Admin\MasterSettingsController@perma_del', 'as' => 'master_settings.perma_del']);
        Route::post('mastersettings/translate', ['uses' => 'Admin\MasterSettingsController@translate', 'as' => 'master_settings.translate']);
        Route::post('mastersettings/vuetranslate', ['uses' => 'Admin\MasterSettingsController@vueTranslate', 'as' => 'master_settings.vuetranslate']);
        
       
        Route::resource('currencies', 'Admin\CurrenciesController');
        Route::get('currencies/{currency_id}/{list?}', [ 'uses' => 'Admin\CurrenciesController@show', 'as' => 'currencies.show' ] );
        Route::post('currencies_mass_destroy', ['uses' => 'Admin\CurrenciesController@massDestroy', 'as' => 'currencies.mass_destroy']);
        Route::post('currencies_restore/{id}', ['uses' => 'Admin\CurrenciesController@restore', 'as' => 'currencies.restore']);
        Route::delete('currencies_perma_del/{id}', ['uses' => 'Admin\CurrenciesController@perma_del', 'as' => 'currencies.perma_del']);
        Route::get('currency/makedefault/{id}', [ 'uses' => 'Admin\CurrenciesController@makeDefault', 'as' => 'currency.makedefault' ]);
        Route::get('currency/update-rates', [ 'uses' => 'Admin\CurrenciesController@updateRates', 'as' => 'currency.update_rates' ]);
        
        Route::resource('email_templates', 'Admin\EmailTemplatesController');
        Route::post('email_templates_mass_destroy', ['uses' => 'Admin\EmailTemplatesController@massDestroy', 'as' => 'email_templates.mass_destroy']);
        Route::post('email_templates_restore/{id}', ['uses' => 'Admin\EmailTemplatesController@restore', 'as' => 'email_templates.restore']);
        Route::delete('email_templates_perma_del/{id}', ['uses' => 'Admin\EmailTemplatesController@perma_del', 'as' => 'email_templates.perma_del']);


        Route::resource('accounts', 'Admin\AccountsController');
        Route::post('accounts_mass_destroy', ['uses' => 'Admin\AccountsController@massDestroy', 'as' => 'accounts.mass_destroy']);
        Route::post('accounts_restore/{id}', ['uses' => 'Admin\AccountsController@restore', 'as' => 'accounts.restore']);
        Route::delete('accounts_perma_del/{id}', ['uses' => 'Admin\AccountsController@perma_del', 'as' => 'accounts.perma_del']);
        Route::get('accounts/{account_id}/{list?}', [ 'uses' => 'Admin\AccountsController@show', 'as' => 'accounts.show' ] );

        Route::resource('payment_gateways', 'Admin\PaymentGatewaysController');
        Route::post('payment_gateways_mass_destroy', ['uses' => 'Admin\PaymentGatewaysController@massDestroy', 'as' => 'payment_gateways.mass_destroy']);
        Route::post('payment_gateways_restore/{id}', ['uses' => 'Admin\PaymentGatewaysController@restore', 'as' => 'payment_gateways.restore']);
        Route::delete('payment_gateways_perma_del/{id}', ['uses' => 'Admin\PaymentGatewaysController@perma_del', 'as' => 'payment_gateways.perma_del']);
        Route::resource('warehouses', 'Admin\WarehousesController');
        Route::get('warehouses/{id}/{list?}', [ 'uses' => 'Admin\WarehousesController@show', 'as' => 'warehouses.show' ] );
        Route::post('warehouses_mass_destroy', ['uses' => 'Admin\WarehousesController@massDestroy', 'as' => 'warehouses.mass_destroy']);
        Route::post('warehouses_restore/{id}', ['uses' => 'Admin\WarehousesController@restore', 'as' => 'warehouses.restore']);
        Route::delete('warehouses_perma_del/{id}', ['uses' => 'Admin\WarehousesController@perma_del', 'as' => 'warehouses.perma_del']);
        Route::resource('taxes', 'Admin\TaxesController');
        Route::post('taxes_mass_destroy', ['uses' => 'Admin\TaxesController@massDestroy', 'as' => 'taxes.mass_destroy']);
        Route::post('taxes_restore/{id}', ['uses' => 'Admin\TaxesController@restore', 'as' => 'taxes.restore']);
        Route::delete('taxes_perma_del/{id}', ['uses' => 'Admin\TaxesController@perma_del', 'as' => 'taxes.perma_del']);
        Route::get('taxes/{tax_id}/{list?}', [ 'uses' => 'Admin\TaxesController@show', 'as' => 'taxes.show' ] );

        Route::resource('discounts', 'Admin\DiscountsController');
        Route::post('discounts_mass_destroy', ['uses' => 'Admin\DiscountsController@massDestroy', 'as' => 'discounts.mass_destroy']);
        Route::post('discounts_restore/{id}', ['uses' => 'Admin\DiscountsController@restore', 'as' => 'discounts.restore']);
        Route::delete('discounts_perma_del/{id}', ['uses' => 'Admin\DiscountsController@perma_del', 'as' => 'discounts.perma_del']);
        Route::get('discounts/{discount_id}/{list?}', [ 'uses' => 'Admin\DiscountsController@show', 'as' => 'discounts.show' ] );

        Route::resource('languages', 'Admin\LanguagesController');
        Route::post('languages_mass_destroy', ['uses' => 'Admin\LanguagesController@massDestroy', 'as' => 'languages.mass_destroy']);
        Route::post('languages_restore/{id}', ['uses' => 'Admin\LanguagesController@restore', 'as' => 'languages.restore']);
        Route::delete('languages_perma_del/{id}', ['uses' => 'Admin\LanguagesController@perma_del', 'as' => 'languages.perma_del']);
        Route::get('userlanguage/changedirection/{id}', [ 'uses' => 'Admin\LanguagesController@changeDirection', 'as' => 'language.changedirection' ]);
        
        Route::resource('articles', 'Admin\ArticlesController');
        Route::get('list_articles/{type?}/{type_id?}', [ 'uses' => 'Admin\ArticlesController@index', 'as' => 'list_articles.index'] );
        
        Route::post('articles_mass_destroy', ['uses' => 'Admin\ArticlesController@massDestroy', 'as' => 'articles.mass_destroy']);
        Route::get('article/{type}/{catid?}', ['uses' => 'Admin\ArticlesController@index', 'as' => 'articles.search']);
        
        Route::resource('countries', 'Admin\CountriesController');
        Route::get('countries/{id}/{list?}', [ 'uses' => 'Admin\CountriesController@show', 'as' => 'countries.show' ] );
        Route::post('countries_mass_destroy', ['uses' => 'Admin\CountriesController@massDestroy', 'as' => 'countries.mass_destroy']);
        Route::post('countries_restore/{id}', ['uses' => 'Admin\CountriesController@restore', 'as' => 'countries.restore']);
        Route::delete('countries_perma_del/{id}', ['uses' => 'Admin\CountriesController@perma_del', 'as' => 'countries.perma_del']);
        Route::post('/spatie/media/upload', 'Admin\SpatieMediaController@create')->name('media.upload');
        Route::post('/spatie/media/remove', 'Admin\SpatieMediaController@destroy')->name('media.remove');

        Route::model('messenger', 'App\MessengerTopic');
        Route::get('messenger/inbox', 'Admin\MessengerController@inbox')->name('messenger.inbox');
        Route::get('messenger/outbox', 'Admin\MessengerController@outbox')->name('messenger.outbox');
        Route::resource('messenger', 'Admin\MessengerController');

        Route::post('csv_parse', 'Admin\CsvImportController@parse')->name('csv_parse');
        Route::post('csv_process', 'Admin\CsvImportController@process')->name('csv_process');
        Route::get('csv_download/{path}', 'Admin\CsvImportController@downloadTemplate')->name('csv_download');

        // Custom Routs.
        Route::post('search_products/{type?}', 'Admin\ProductsReturnsController@searchProducts')->name('search_products');
        
        Route::resource('measurement_units', 'Admin\MeasurementUnitsController');
        Route::post('measurement_units_mass_destroy', ['uses' => 'Admin\MeasurementUnitsController@massDestroy', 'as' => 'measurement_units.mass_destroy']);
        Route::post('measurement_units_restore/{id}', ['uses' => 'Admin\MeasurementUnitsController@restore', 'as' => 'measurement_units.restore']);
        Route::delete('measurement_units_perma_del/{id}', ['uses' => 'Admin\MeasurementUnitsController@perma_del', 'as' => 'measurement_units.perma_del']);

        Route::get('mastersettings/settings/', 'Admin\GeneralSettingsController@index');
        Route::get('mastersettings/settings/index', 'Admin\GeneralSettingsController@index');
        Route::get('mastersettings/settings/add', 'Admin\GeneralSettingsController@create');
        Route::post('mastersettings/settings/add', 'Admin\GeneralSettingsController@store');
        Route::get('mastersettings/settings/edit/{slug}', 'Admin\GeneralSettingsController@edit');
        Route::patch('mastersettings/settings/edit/{slug}', 'Admin\GeneralSettingsController@update');
        Route::get('mastersettings/settings/view/{slug}', 'Admin\GeneralSettingsController@viewSettings');
        Route::get('mastersettings/settings/add-sub-settings/{slug}', 'Admin\GeneralSettingsController@addSubSettings');
        Route::post('mastersettings/settings/add-sub-settings/{slug}', 'Admin\GeneralSettingsController@storeSubSettings');
        Route::patch('mastersettings/settings/add-sub-settings/{slug}', 'Admin\GeneralSettingsController@updateSubSettings');

        Route::resource('navigation_menues', 'Admin\NavigationMenuesController');
        Route::post('navigation_menues/additem', [ 'uses' => 'Admin\NavigationMenuesController@additem', 'as' => 'navigation_menues.additem'] );
        Route::post('navigation_menues/updateitem', [ 'uses' => 'Admin\NavigationMenuesController@updateitem', 'as' => 'navigation_menues.updateitem'] );
        
        Route::get('search', 'MegaSearchController@search')->name('mega-search');
        Route::post('get-details', 'MegaSearchController@getDetails')->name('get-mega-details');
        Route::get('language/{lang}', function ($lang) {
            $language = \App\Language::where('code', '=', $lang)->first();
            $direction = 'ltr';
            if ( $language && 'Yes' === $language->is_rtl ) {
                $direction = 'rtl';
            }
            return redirect()->back()
                ->withCookie(cookie()->forever('language', $lang))
                ->withCookie(cookie()->forever('direction', $direction));
                ;
        })->name('language');

        Route::get('/dashboard', [ 'uses' => 'HomeController@dashboard', 'as' => 'home.dashboard' ]);
        Route::post('/home/load-modal', [ 'uses' => 'HomeController@loadModal', 'as' => 'home.load-modal' ]);
        
        Route::get('/media/download/{media_id}', [ 'uses' => 'HomeController@mediaDownload', 'as' => 'home.media-download' ]);
        Route::get('/media/file-download/{model}/{field}/{record_id}/{namespace?}', [ 'uses' => 'HomeController@mediaFileDownload', 'as' => 'home.media-file-download' ]);

        Route::get('/dashboard-widgets/{role_id?}', [ 'uses' => 'HomeController@dashboardWidgets', 'as' => 'home.dashboard-widgets' ]);
        Route::get('/dashboard-widgets-all', [ 'uses' => 'HomeController@dashboardWidgetsAll', 'as' => 'home.dashboard-widgets-all' ]);

        Route::get('/dashboard/widgets/add/{id?}', [ 'uses' => 'HomeController@dashboardWidgetsAdd', 'as' => 'home.dashboard-widgets-add' ]);
        Route::post('/dashboard/widgets/add/{id?}', [ 'uses' => 'HomeController@dashboardWidgetsAdd', 'as' => 'home.dashboard-widgets-store' ]);
        
        Route::get('/dashboard/widgets/change-order/{role_id?}', [ 'uses' => 'HomeController@dashboardWidgetsChangeorder', 'as' => 'home.dashboard-widgets-changeorder' ]);
        Route::post('/dashboard/widgets/change-order/{role_id?}', [ 'uses' => 'HomeController@dashboardWidgetsChangeorder', 'as' => 'home.dashboard-widgets-changeorder-store' ]);
        Route::post('/dashboard/widgets/change-status', [ 'uses' => 'HomeController@dashboardWidgetsStatus', 'as' => 'home.dashboard-widgets-change-status' ]);

        Route::get('/dashboard/widgets/assign/{role_id}', [ 'uses' => 'HomeController@dashboardWidgetsAssign', 'as' => 'home.dashboard-widgets-assign' ]);
        Route::post('/dashboard/widgets/assign/{role_id}', [ 'uses' => 'HomeController@dashboardWidgetsAssign', 'as' => 'home.dashboard-widgets-assign-store' ]);

        Route::delete('/dashboard-widgets/delete/{id}', ['uses' => 'HomeController@deleteWidget', 'as' => 'home.dashboard-widgets-delete']);

        Route::match(['get', 'post'], '/system-reset', ['uses' => 'HomeController@systemReset', 'as' => 'home.system-reset']);
        
        Route::get('/fake-data', [ 'uses' => 'HomeController@fakeDataFunctions', 'as' => 'home.fake-data' ]);
    });
});

Route::fallback('HomeController@dashboard');