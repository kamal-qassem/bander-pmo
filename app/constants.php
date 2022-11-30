<?php 
$base1 = $base = '';

if ( isset( $_SERVER['HTTP_HOST'] ) || isset( $_SERVER['HTTPS'] ) ) {
	$base1 = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
	$base1 .= '://'.$_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
	$base = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
	$base .= '://'.$_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
}

define('PREFIX1', $base1);
define('BASE_PATH', $base.'/');
define('PREFIX', $base);

define('COLUMNS', 3);


/**
 * These are Database record values. We need to keep these ids in the fresh system.
*/
define('ADMIN_TYPE', 1);
define('CUSTOMERS_TYPE', 2);
define('BUSINESS_MANAGER_TYPE', 3);
define('SALES_MANAGER_TYPE', 4);
define('CONTACT_SALE_AGENT', 5);
define('PROJECT_MANAGER', 6);
define('STOCK_MANAGER', 7);
define('SUPPLIERS_TYPE', 8);
define('CONTACT_CLIENT_TYPE', 10);
define('LEADS_TYPE', 11);
define('EMPLOYEES_TYPE', 12);
define('EXECUTIVE_TYPE', 14);

define('STATUS_COMPLETED', 11);
define('DEFAULT_CONTACT_TYPE', 2); //Customer
define('DEFAULT_LANGUAGE', 1); // English
define('DEFAULT_GROUP', 1); // Default
define('DEFAULT_CURRENCY_ID', 1); // USD

define('PROJECT_STATUS_COMPLETED', 4); // Finished

define('PROJECT_TASK_STATUS_OPEN', 12);
define('PROJECT_TASK_STATUS_PROGRESS', 4);
define('PROJECT_TASK_STATUS_NOTSTARTED', 5);
define('PROJECT_TASK_STATUS_COMPLETED', 11);

define('SUPPORT_STATUS_OPEN', 11);
define('SUPPORT_STATUS_COMPLETED', 10);

define('CACHE_MINUTES', 60);

// Role Constants.
define('ROLE_ADMIN', 'Admin');
define('ROLE_CUSTOMER', 'Customer');
define('ROLE_BUSINESS_MANAGER', 'BusinessManager');
define('ROLE_SALES_MANAGER', 'SalesManager');
define('ROLE_SALES_PERSON', 'SalesPerson');
define('ROLE_PROJECT_MANAGER', 'ProjectManager');
define('ROLE_STOCK_MANAGER', 'StockManager');
define('ROLE_CLIENT', 'Client');
define('ROLE_EMPLOYEE', 'Employee');
define('ROLE_SUPPLIER', 'Supplier');
define('ROLE_EXECUTIVE', 'Executive');

// Project Constants.
define('PROJECT_BILLING_TYPE_FIXED_PRICE', 1);
define('PROJECT_BILLING_TYPE_PROJECT_HOURS', 2);
define('PROJECT_BILLING_TYPE_TASK_HOURS', 3);

// Constants
define('URL_TRANSLATIONS', PREFIX . 'admin/translations');
define('CSS', PREFIX1.'css/');
define('JS', PREFIX1.'js/');
define('UPLOADS', PREFIX1.'uploads/');
define('IMAGE_PATH_SETTINGS', UPLOADS.'settings/');
define('ADMINLTE', PREFIX1.'adminlte/');

//SETTINGS MODULE
define('URL_SETTINGS_LIST', PREFIX.'admin/mastersettings/settings');
define('URL_SETTINGS_VIEW', PREFIX.'admin/mastersettings/settings/view/');
define('URL_SETTINGS_ADD', PREFIX.'admin/mastersettings/settings/add');
define('URL_SETTINGS_EDIT', PREFIX.'admin/mastersettings/settings/edit/');
define('URL_SETTINGS_DELETE', PREFIX.'admin/mastersettings/settings/delete/');
define('URL_SETTINGS_GETLIST', PREFIX.'mastersettings/settings/getList/');
define('URL_SETTINGS_ADD_SUBSETTINGS', PREFIX.'admin/mastersettings/settings/add-sub-settings/');

define('PAYMENT_STATUS_CANCELLED', 'cancelled');
define('PAYMENT_STATUS_SUCCESS', 'success');
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_ABORTED', 'aborted');
define('PAYMENT_RECORD_MAXTIME', '30'); //TIME IN MINUTES

define('TASK_STATUS_COMPLETED', 8);
if ( ! defined('ROLE_ADMIN')) {
	define('ROLE_ADMIN', 'Admin');
}
define('FILE_TYPES_GENERAL', 'png,jpg,jpeg,gif,pdf,doc,docx,xls,xlsx,dot,ppt,pptx,odt,ods,odp,zip');
define('FILE_MIME_TYPES_GENERAL', 'image/png,image/jpeg,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.oasis.opendocument.text,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.presentation');
define('FILE_TYPES_GALLERY', 'png,jpg,jpeg,gif');

// Project tabs constants
define('PROJECT_TASKS_VIEW', 1); // Tasks
define('PROJECT_TASKS_CREATE', 2);
define('PROJECT_TASKS_EDIT', 3);
define('PROJECT_TASKS_COMMENT_CREATE', 4);
define('PROJECT_TASKS_COMMENT_VIEW', 5);
define('PROJECT_TASKS_ATTACHMENTS', 6);
define('PROJECT_TASKS_CHECKLIST', 7);
define('PROJECT_TASKS_ATTACHMENTS_UPLOAD', 8);
define('PROJECT_TASKS_LOGGEDTIME_VIEW',9);
define('PROJECT_FINANCE_VIEW', 10);

define('PROJECT_UPLOAD_FILES', 11);
define('PROJECT_OPEN_DISCUSSIONS', 12);
define('PROJECT_MILESTONES_VIEW', 13); //admin/mile_stones
define('PROJECT_GANTT_VIEW', 14);
define('PROJECT_TIMESHEETS_VIEW', 15); // Time entries
define('PROJECT_ACTIVITYLOG_VIEW', 16);
define('PROJECT_TEAMMEMBERS_VIEW', 17);
define('PROJECT_TICKETS_VIEW', 18);
define('PROJECT_SALES_VIEW', 19);
define('PROJECT_NOTES_VIEW', 20);

define('PAYMENT_METHOD_OFFLINE', 'Offline');

define('DEFAULT_ADMIN_ID', 1);
define('DEFAULT_EXECUTIVE_ID', 2);
define('DEFAULT_CUSTOMER_ID', 3);
define('DEFAULT_SALEAGENT_ID', 4);
define('DEFAULT_SUPPLIER_ID', 5);
define('DEFAULT_SALEMANAGER_ID', 6);
define('DEFAULT_EMPLOYEE_ID', 7);
define('DEFAULT_CLIENT_ID', 8);
define('DEFAULT_PROJECTMANAGER_ID', 9);
define('DEFAULT_BUSINESSMANAGER_ID', 10);
define('DEFAULT_STOCKMANAGER_ID', 11);