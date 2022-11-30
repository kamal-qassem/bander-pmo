<?php

return array (
  'user-management' => 
  array (
    'title' => 'User management',
  ),
  'permissions' => 
  array (
    'title' => 'Permissions',
    'fields' => 
    array (
      'title' => 'Title',
    ),
  ),
  'roles' => 
  array (
    'title' => 'Roles',
  'title-module' => 'Modules',
    'fields' => 
    array (
      'title' => 'Title',
      'permission' => 'Permissions',
    'module' => 'Module',
    ),
  ),
  'users' => 
  array (
    'title' => 'Users',
    'active' => 'Active',
    'suspend' => 'Suspend',
    'activate' => 'Activate',
  'action' => 'Action',
  'password_help' => 'If you leave it blank, system will generate password automatically',
  'password_help_update' => 'If you leave it blank, system won\'t change password.',
  'hourly_rate_help' => 'If you are assigning Employee role to this user, you need to enter hourly',
  'default' => 'Default',
  'darkgray-theme' => 'Darkgray theme',
  'gradient-blue-theme' => 'Gradient blue theme',
  'light-blue-theme' => 'Light blue theme',
  'white' => 'White',
  'skin-blue-light' => 'Skin blue light',
  'skin-yellow' => 'Skin yellow',
  'skin-yellow-light' => 'Skin yellow light',
  'skin-green' => 'Skin green',
  'skin-green-light' => 'Skin green light',
  'skin-purple' => 'Skin purple',
  'skin-purple-light' => 'Skin purple light',
  'skin-red' => 'Skin red',
  'skin-black' => 'Skin black',
  'skin-black-light' => 'Skin black light',
  'skin-red-light' => 'Skin red light',
  
    'fields' => 
    array (
      'name' => 'Name',
      'email' => 'Email',
      'password' => 'Password',
      'role' => 'Role',
      
      'remember-token' => 'Remember token',
      'contact-reference' => 'Contacts',
      'department' => 'Department',
      'status' => 'Status',
    'hourly_rate' => 'Hourly rate',
    'color-theme' => 'Color theme',
    'color-skin' => 'Color skin',
   
    ),
  ),
  'user-actions' => 
  array (
    'title' => 'User actions',
    'created_at' => 'Time',
    'fields' => 
    array (
      'user' => 'User',
      'action' => 'Action',
      'action-model' => 'Action model',
      'action-id' => 'Action id',
    ),
  ),
  'contact-management' => 
  array (
    'title' => 'Contact management',
  ),
  'contact-companies' => 
  array (
    'title' => 'Companies',
    'fields' => 
    array (
      'name' => 'Company name',
      'email' => 'Email',
      'address' => 'Address',
      'website' => 'Website',
    ),
  ),
  'contacts' => 
  array (
  'title' => 'Contacts',
  'mailchimp-email-campaigns' => 'Mailchimp email campaigns',
  'mailchimp-lists' => 'Mailchimp lists',
  'member-count' => 'Member count',
  'member-status' => 'Status',
  'is-schedule' => 'Is schedule?',
  'last-run' => 'Last run',
  'send-bulk-emails' => 'Send bulk emails',
  'send_after' => 'Send after',
  'value-in-minutes' => 'Value in minutes',

  'title_contact' => 'Contact',
  'title_customers' => 'Customers',
  'title_customer' => 'Customer',
  'title_suppliers' => 'Suppliers',
  'title_employees' => 'Employees',
  'title_supplier' => 'Supplier',
  'title_employee' => 'Employee',
  'title_leads' => 'Leads',
  'title_lead' => 'Lead',

  'title_clients' => 'Clients',
  'title_client' => 'Client',
  'title_saleagents' => 'Sale agents',
  'title_saleagent' => 'Sale agent',

  'title_profile' => 'Profile',
  'title_delivery_address' => 'Delivery/shipping address',
  'edit-profile' => 'Edit Profile',
  'update-delivery-address' => 'Update delivery address',
  'delivery_address_updated' => 'Delivery address updated',
  'update-profile' => 'Update profile',
  'title_shipping_address' => 'Shipping address',
  'shipping_address_updated' => 'Shipping address updated',
  'site-language-help' => 'This will help if the contact has login access',
  'address' => 'Address',
  'convert-to' => 'Convert to',
    'fields' => 
    array (
      'company' => 'Company',
      'theme' => 'Theme',
      'group' => 'Group',
      'contact-type' => 'Contact type',
      'first-name' => 'First name',
      'last-name' => 'Last name',
      'language' => 'Language',
      'phone1' => 'Phone 1',
      'phone2' => 'Phone 2',
      'phone1_code' => 'Phone 1 Code',
      'phone2_code' => 'Phone 2 Code',
      'email' => 'Email',
      'skype' => 'Skype',
      'address' => 'Address',
      'city' => 'City',
      'state-region' => 'State/region',
      'zip-postal-code' => 'Zip/postal code',
      'tax-id' => 'Tax ID',
      'country' => 'Country',
      'create_user' => 'Create User for this contact?',
    'site-language' => 'Site language',
    'name' => 'Name',
    'color-theme' => 'Color theme',
    'alert-msg' => 'Alert!
      Once the record is deleted all the data related to the contact will be deleted permanently and it cannot be recovered.',
    'alert-msg-continue' => 'Do you want to continue?',
    'record-info' => 'Record info',
    'invoices-count' => 'Invoices count',
    'quotes-count' => 'Quotes count',
    'orders-count' => 'Orders count',
    'credit-notes-count' => 'Credit notes count',
    ),
  ),
  'expense-management' => 
  array (
    'title' => 'Accounting',
  ),
  'expense-category' => 
  array (
    'title' => 'Expense categories',
    'fields' => 
    array (
      'name' => 'Name',
    ),
  ),
  'income-category' => 
  array (
    'title' => 'Income categories',
    'fields' => 
    array (
      'name' => 'Name',
    ),
  ),
  'income' => 
  array (
    'title' => 'Income',
    'title-incomes' => 'Incomes',
    'fields' => 
    array (
      'account' => 'Account',
      'income-category' => 'Income category',
      'entry-date' => 'Entry date',
      'amount' => 'Amount',
      'description' => 'Description',
      'description-file' => 'Description file',
      'payer' => 'Payer',
      'pay-method' => 'Pay method',
      'ref-no' => 'Reference no / Transaction id',
    ),
  ),
  'expense' => 
  array (
    'title' => 'Expenses',
    'no-suffient-funds' => 'Sufficient funds are not available in selected account',
  'currency-help' => 'When you add an expense to your company (not a customer) – base currency is used.<br>
When you add an expense to a customer and it’s not billable – base currency is used.<br>
When you add an expense to a customer and its billable – customer currency is used<b>
When you add expense linked to project – the project currency is used (either customer currency if configured or base currency)',
    'fields' => 
    array (
      'account' => 'Account',
      'expense-category' => 'Expense category',
      'entry-date' => 'Entry date',
      'amount' => 'Amount',
      'description' => 'Description',
      'description-file' => 'Description file',
      'payee' => 'Payee',
      'recurring-period' => 'Recurring period',
      'recurring-value' => 'Recurring value',
      'recurring-type' => 'Recurring type',
      'cycles' => 'Cycles',
      'total-cycles' => 'Total cycles',
      'currency'     => 'Currency',
      'payment-method' => 'Payment method',
      'ref-no' => 'Reference no / Transaction id',
    
    'name' => 'Name',
    'is_recurring' => 'Is recurring',
    'recurring_period_id' => 'Recurring period',
    'recurring_value' => 'Recurring value',
    'create_invoice_billable' => 'Create invoice billable',
    'send_invoice_to_customer' => 'Send invoice to customer',
    'billable' => 'Billable',
    'project' => 'Project',
    ),
  ),
  'monthly-report' => 
  array (
    'title' => 'Monthly report',
  ),
  'faq-management' => 
  array (
    'title' => 'FAQ Management',
    'faq' => 'FAQs',
  ),
  'faq-categories' => 
  array (
    'title' => 'Categories',
    'fields' => 
    array (
      'title' => 'Category',
    ),
  ),
  'faq-questions' => 
  array (
    'title' => 'FAQs',
    'fields' => 
    array (
      'category' => 'Category',
      'question-text' => 'Question',
      'answer-text' => 'Answer',
    ),
  ),
  'internal-notifications' => 
  array (
    'title' => 'Quick notifications',
    'fields' => 
    array (
      'text' => 'Text',
      'link' => 'Link',
      'users' => 'Users',
    ),
  ),
  'task-management' => 
  array (
    'title' => 'Task management',
  ),
  'task-statuses' => 
  array (
    'title' => 'Statuses',
    'panel-default' => 'Panel default',
    'panel-primary' => 'Panel primary',
    'panel-success' => 'Panel success',
    'panel-info' => 'Panel info',
    'panel-warning' => 'Panel warning',
    'panel-danger' => 'Panel danger',
    'fields' => 
    array (
      'name' => 'Name',
      'color' => 'Color',
    ),
  ),
  'task-tags' => 
  array (
    'title' => 'Tags',
    'fields' => 
    array (
      'name' => 'Name',
    ),
  ),
  'tasks' => 
  array (
    'title' => 'Tasks',
    'task-status-updated' => 'Task status updated successfully',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
      'status' => 'Status',
      'tag' => 'Tags',
      'attachment' => 'Attachment',
      'start-date' => 'Start date',
      'thumbnail' => 'Thumbnail',
      'due-date' => 'Due date',
      'user' => 'Assigned to',
    ),
  ),
  'task-calendar' => 
  array (
    'title' => 'Calendar',
    'status-wise' => 'Status wise',
  ),
  'content-management' => 
  array (
    'title' => 'Content management',
  ),
  'content-categories' => 
  array (
    'title' => 'Categories',
    'fields' => 
    array (
      'title' => 'Category',
      'slug' => 'Slug',
    ),
  ),
  'content-tags' => 
  array (
    'title' => 'Tags',
    'fields' => 
    array (
      'title' => 'Tag',
      'slug' => 'Slug',
    ),
  ),
  'content-pages' => 
  array (
    'title' => 'Pages',
    'fields' => 
    array (
      'title' => 'Title',
      'category-id' => 'Categories',
      'tag-id' => 'Tags',
      'page-text' => 'Text',
      'excerpt' => 'Excerpt',
      'featured-image' => 'Featured image',
      'created-at' => 'Time created',
    ),
  ),
  'product-management' => 
  array (
    'title' => 'Product management',
  ),
  'product-categories' => 
  array (
    'title' => 'Categories',
    'fields' => 
    array (
      'name' => 'Category name',
      'description' => 'Description',
      'photo' => 'Photo (max 8mb)',
    ),
  ),
  'product-tags' => 
  array (
    'title' => 'Tags',
    'fields' => 
    array (
      'name' => 'Name',
    ),
  ),
  'products' => 
  array (
    'title' => 'Products',
    'gallery-file-types' => 'Accepted file types: png,jpg,jpeg,gif',
  'fillprices' => 'Fill prices',
  'enter-sale-price' => 'Please enter sale price',
  'enter-actual-price' => 'Please enter actual price',
  'low_quantity' => 'Low quantity',
  'sale-price-lessthan-actual-price' => 'The Sale price should less than the actual price',
  //'sale-price-variation-lessthan-actual-price-variation' => 'The Sale price variation should less than the actual price variation',
  'sale-price-variation-lessthan-actual-price-variation' => 'The Sale price :cur should less than the actual price :cur2',
  'price-fill-instructions' => 'This will fill the other currency prices based on base currency',
    'fields' => 
    array (
      'name' => 'Product name',
      'product-code' => 'Product code',
      'actual-price' => 'Actual price',
      'sale-price' => 'Sale price',
      'category' => 'Category',
      'tag' => 'Tag',
      'enter-sale-price' => 'Please enter sale price',
      'enter-actual-price' => 'Please enter actual price',
      'ware-house' => 'Ware house',
      'description' => 'Description',
      'excerpt' => 'Excerpt',
      'stock-quantity' => 'Stock quantity',
      'alert-quantity' => 'Alert quantity',
      'image-gallery' => 'Image gallery',
      'thumbnail' => 'Thumbnail',
      'other-files' => 'Other files',
      'hsn-sac-code' => 'HSN/SAC code',
      'product-size' => 'Product size',
      'product-weight' => 'Product weight',
      'brand' => 'Brand',
    ),
  ),
  'assets-management' => 
  array (
    'title' => 'Assets management',
  ),
  'assets-categories' => 
  array (
    'title' => 'Categories',
    'fields' => 
    array (
      'title' => 'Title',
    ),
  ),
  'assets-statuses' => 
  array (
    'title' => 'Statuses',
    'fields' => 
    array (
      'title' => 'Title',
    ),
  ),
  'assets-locations' => 
  array (
    'title' => 'Locations',
    'fields' => 
    array (
      'title' => 'Title',
    ),
  ),
  'assets' => 
  array (
    'title' => 'Assets',
    'fields' => 
    array (
      'category' => 'Category',
      'serial-number' => 'Serial number',
      'title' => 'Title',
      'photo1' => 'Thumbnail',
      'photo2' => 'Gallery',
      'attachments' => 'Attachments',
      'status' => 'Status',
      'location' => 'Location',
      'assigned-user' => 'Assigned (user)',
      'notes' => 'Notes',
    ),
  ),
  'assets-history' => 
  array (
    'title' => 'Assets history',
    'created_at' => 'Time',
    'fields' => 
    array (
      'asset' => 'Asset',
      'status' => 'Status',
      'location' => 'Location',
      'assigned-user' => 'Assigned (user)',
    ),
  ),
  'coupon-management' => 
  array (
    'title' => 'Coupon Management',
  ),
  'coupon-campaigns' => 
  array (
    'title' => 'Campaigns',
    'fields' => 
    array (
      'title' => 'Title',
      'description' => 'Description',
      'valid-from' => 'Valid from',
      'valid-to' => 'Valid to',
      'discount-amount' => 'Discount amount',
      'discount-percent' => 'Discount percent',
      'coupons-amount' => 'Coupons amount',
    ),
  ),
  'coupons' => 
  array (
    'title' => 'Coupons',
    'fields' => 
    array (
      'campaign' => 'Campaign',
      'code' => 'Code',
      'valid-from' => 'Valid from',
      'valid-to' => 'Valid to',
      'discount-amount' => 'Discount amount',
      'discount-percent' => 'Discount percent',
      'redeem-time' => 'Redeem time',
    ),
  ),
  'expense-types' => 
  array (
    'title' => 'Expense types',
  ),
  'global-settings' => 
  array (
    'title' => 'Global settings',
  ),
  'currencies' => 
  array (
    'title' => 'Currencies',
    'fields' => 
    array (
      'name' => 'Name',
      'symbol' => 'Symbol',
      'code' => 'Code',
      'rate' => 'Rate',
      'status' => 'Status',
      'is_default' => 'Is Default?',
    ),
  ),
  'sales-taxes' => 
  array (
    'title' => 'Sales taxes',
  ),
  'email-templates' => 
  array (
    'title' => 'Email templates',
    'fields' => 
    array (
      'name' => 'Name',
      'subject' => 'Subject',
      'body' => 'Body',
    ),
  ),
  'companies' => 
  array (
    'title' => 'Companies',
    'fields' => 
    array (
      'company-name' => 'Company name',
      'address' => 'Address',
      'business-number' => 'Business number',
      'city' => 'City',
      'url' => 'Url',
      'state-region' => 'State/region',
      'email' => 'Email',
      'zip-postal-code' => 'Zip/postal code',
      'country' => 'Country',
      'phone' => 'Phone',
      'logo' => 'Logo',
    ),
  ),
  'accounts' => 
  array (
    'title' => 'Accounts',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
      'initial-balance' => 'Initial balance',
      'account-number' => 'Account number',
      'contact-person' => 'Contact person',
      'phone' => 'Phone',
      'url' => 'URL',
    ),
  ),
  'payment-gateways' => 
  array (
    'title' => 'Payment gateways',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
      'logo' => 'Logo',
    ),
  ),
  'warehouses' => 
  array (
    'title' => 'Warehouses',
    'fields' => 
    array (
      'name' => 'Name',
      'address' => 'Address',
      'description' => 'Description',
    ),
  ),

   'ware-houses' => 
  array (
    'title' => 'Warehouses',
  ),

  'taxes' => 
  array (
    'title' => 'Taxes',
    'fields' => 
    array (
      'name' => 'Name',
      'rate' => 'Rate',
      'rate-type' => 'Rate type',
      'description' => 'Description',
    ),
  ),
  'discounts' => 
  array (
    'title' => 'Discounts',
    'fields' => 
    array (
      'name' => 'Name',
      'discount' => 'Discount',
      'discount-type' => 'Discount type',
      'description' => 'Description',
    ),
  ),
  'recurring-periods' => 
  array (
    'title' => 'Recurring periods',
    'fields' => 
    array (
      'title' => 'Title',
      'value' => 'Value',
      'type' => 'Type',
      'description' => 'Description',
    ),
  ),
  'dynamic-options' => 
  array (
    'title' => 'Dynamic options',
  'priorities' => 'Priority',
  'quotes' => 'Quotes',
  'proposals' => 'Proposals',
  'contracts' => 'Contracts',
  'project-tasks' => 'Project tasks',
  'proposals' => 'Proposals',
  'invoices' => 'Invoices',
  'taskstatus' => 'Task status',
  'projecttaskstatus' => 'Project task status',
  'projecttaskpriorities' => 'Project task priorities',
  
    'fields' => 
    array (
      'title' => 'Title',
      'module' => 'Module',
      'type' => 'Type',
      'description' => 'Description',
    'color' => 'Color',
    ),
  ),
  'languages' => 
  array (
    'title' => 'Languages',
    'fields' => 
    array (
      'language' => 'Language',
      'code' => 'Code',
      'is-rtl' => 'Is rtl',
    ),
  ),
  'purchase-orders' => 
  array (
    'title' => 'Purchase orders',
    'fields' => 
    array (
      'customer' => 'Supplier',
      'paymentstatus' =>'Payment status',
      'subject' => 'Subject',
      'status' => 'Status',
      'address' => 'Address',
      'invoice-prefix' => 'Purchase order prefix',
      'show-quantity-as' => 'Show quantity as',
      'invoice-no' => 'Purchase order: #',
      'reference' => 'Reference',
      'order-date' => 'Purchase order issued date',
      //'order-due-date' => 'Purchase order due date',
      'order-due-date' => 'Estimated time of arrival',
      'update-stock' => 'Update stock?',
      'stock-updated' => 'Stock updated',
      'stock-not-updated' => 'No updated',
      'notes' => 'Notes',
      'currency' => 'Currency',
      'warehouse' => 'Warehouse',
      'selected-supplier-delivery-address' => 'Selected supplier/Shipping address',
      'tax' => 'Tax',
      'discount' => 'Discount',
      'amount' => 'Amount',
      
    ),
  ),
  'manage-projects' => 
  array (
    'title' => 'Manage projects',
  ),
  'projects' => 
  array (
    'title' => 'Projects',
  ),
  'project-statuses' => 
  array (
    'title' => 'Project statuses',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
    ),
  ),
  'client-projects' => 
  array (
    'title' => 'Client projects',
    'ticket' => 'Ticket',
  'task_total_logged_time' => 'Task total logged time',
  'credit-note-from-project' => 'Credit note from project',
  'title-tickets' => 'Tickets',
  'subject' => 'Subject',
  'status' => 'Status',
  'last-updated' => 'Last updated',
  'agent' => 'Agent',
  'contact' => 'Contact',
  'created' => 'Created',
  'category' => 'Category',
  'expenses' => 'Expenses',
  'invoices' => 'Invoices',
  'invoice-project' => 'Invoice project',
  'expense-created-from-project' => 'Expense created from project',
  'invoice-data-type' => 'Invoice data type',
  'single_line_help' => '<b>Item name:</b> Project name<br /><b>Description:</b> All tasks + total logged time per task',
  'task_per_item_help' => '<b>Item name:</b> Project name + Task name<br /><b>Description:</b> Total logged time',
  'timesheets_include_notes_help' => '<b>Item name:</b> Project name + Task name<br /><b>Description:</b> Timesheet start time + end time + total logged time',
  'select-all-tasks' => 'Select all tasks',
  'select-all-expenses' => 'Select all expenses',
  'marked-as-complete' => 'All billed tasks will be marked as finished',
  'not-billed' => '(Not billed)',
  'billed' => '(Billed)',
  'progress' => 'Progress',
  'open-tasks' => 'Open tasks',
  'days-left' => 'Days left',
  'logged-hours' => 'Logged hours',
  'billable-hours' => 'Billable hours',
  'billed-hours' => 'Billed hours',
  'unbilled-hours' => 'Unbilled-hours',
  'expenses' => 'Expenses',
  'expenses-billable' => 'Expenses billable',
  'expenses-billed' => 'Expenses billed',
  'expenses-unbilled' => 'Expenses unbilled',
  'timer-started' => 'Timer started successfully',
  'timer-stopped' => 'Timer stopped successfully',
  'start-timer' => 'Start timer',
  'stop-timer' => 'Stop timer',
  'start-timer-info' => 'You need to be assigned on this task to start timer!',
  'invoice-created-from-project' => 'Invoice created from a project :title',
  
  'details' => 'Details',
  'tasks' => 'Tasks',
  'timesheets' => 'Timesheets',
  'milestones' => 'Milestones',
  'files' => 'Files',
  'discussions' => 'Discussions',
  'tickets' => 'Tickets',
  'notes' => 'Notes',
  'time-entries' => 'Time entries',
  
    'fields' => 
    array (
      'title' => 'Title',
      'client' => 'Client',
      'priority' => 'Priority',
      'budget' => 'Budget',
      'billing-type' => 'Billing type',
      'phase' => 'Phase',
      'assigned-to' => 'Assigned to',
      'start-date' => 'Start date',
      'due-date' => 'Due date',
      'status' => 'Status',
      'description' => 'Description',
      'demo-url' => 'Demo url',
      'project-tabs' => 'Visible tabs',
    'hourly_rate' => 'Hourly rate',
    'project_rate_per_hour' => 'Project rate per hour',
    'estimated_hours' => 'Estimated hours',
    'progress_from_tasks' => 'Progress from tasks?'
    ),
  ),
  'project-billing-types' => 
  array (
    'title' => 'Project billing types',
    'fields' => 
    array (
      'title' => 'Title',
      'description' => 'Description',
    ),
  ),
  'time-entries' => 
  array (
    'title' => 'Time entries',
  'billed' => 'Billed',
  'project-task' => 'Project task',
  'not-billed' => 'Not billed',
    'fields' => 
    array (
      'project' => 'Project',
      'start-date' => 'Start date',
      'end-date' => 'End date',
      'description' => 'Description',
    ),
  ),
  'project-tabs' => [
    'title' => 'Project tabs',
    'fields' => [
      'title' => 'Title',
      'description' => 'Description',
    ],
  ],
  'sales' => 
  array (
    'title' => 'Sales',
  ),
  'invoices' => 
  array (
    'title' => 'Invoices',
    'fields' => 
    array (
      'customer' => 'Customer',
      'invoice-balance-due' => 'Invoice balance due',
      'start-date' => 'Start date',
      'due-date' => 'Due date',
      'quote-date' => 'Quote date',
      'quote-expiry-date' => 'Quote expiry date',
      'contract-date' => 'Contract date',
      'contract-expiry-date' => 'Contract expiry date',
      'proposal-date' => 'Proposal date',
      'proposal-expiry-date' => 'Proposal expiry date',
      'currency' => 'Currency',
      'title' => 'Title',
      'address' => 'Address',
      'invoice-prefix' => 'Invoice prefix',
      'show-quantity-as' => 'Show quantity as',
      'invoice-no' => 'Invoice #',
      'status' => 'Status',
      'reference' => 'Reference',
      'invoice-date' => 'Invoice date',
      'invoice-due-date' => 'Invoice due date',
      'invoice-notes' => 'Invoice notes',
      'client-notes' => 'Client notes',
      'credit-date'  => 'Credit note date',
      'tax' => 'Additional tax',
      'discount' => 'Additional discount',
      'amount' => 'Amount',
      'discount_format' => 'Discount format',
      'tax_format' => 'Tax format',
      'created-by' => 'Created by',
      'paymentstatus' => 'Payment status',
      'delivery-address' => 'Delivery/Shipping address',
      'show-delivery-address' => 'Show delivery details in invoice',
      'show-delivery-address-proposals'=>'Show delivery details in proposals',
      'show-delivery-address-credit-notes' => 'Show delivery details in credit notes',
      'admin-notes' => 'Admin notes',
      'terms-conditions' => 'Terms & Conditions',
      'prevent-overdue-reminders' => 'Prevent sending overdue reminders for this invoice',
    'prevent-overdue-reminders-order' => 'Prevent sending overdue reminders for this order',
      'allowed-paymodes' => 'Allowed payment modes',
    'sale-agent' => 'Sale agent',
    'client' => 'Client',
    ),
    'selected-customer-address' => 'Selected customer billing address',
    'selected-customer-delivery-address' => 'Selected customer delivery/Shipping address',
    'selected-supplier-delivery-address' => 'Selected supplier delivery/shipping address',
  ),

  //credit notes
   'credit_notes' => 
  array (
    'title' => 'Credit notes',
    'fields' => 
    array (
      'customer' => 'Customer',
      'credit-no' => 'Credit: #',
      'credit-num' => 'Credit #',
      'credit-prefix' => 'Credit prefix',
      'credit-note-date' => 'Credit note date',
      'credit-status' => 'Credit status',
      'currency' => 'Currency',
      'title' => 'Title',
      'address' => 'Address',
      'invoice-prefix' => 'Invoice prefix',
      'show-quantity-as' => 'Show quantity as',
      'status' => 'Status',
      'reference' => 'Reference',
      'invoice-date' => 'Invoice date',
      'invoice-due-date' => 'Invoice due date',
      'invoice-notes' => 'Invoice notes',
      'client-notes' => 'Client notes',
      'tax' => 'Additional tax',
      'discount' => 'Additional discount',
      'amount' => 'Amount',
      'discount_format' => 'Discount format',
      'tax_format' => 'Tax format',
      'created-by' => 'Created by',
      'paymentstatus' => 'Payment status',
      'delivery-address' => 'Delivery/Shipping address',
      'show-delivery-address' => 'Show delivery details in invoice',
      'admin-notes' => 'Admin notes',
      'terms-conditions' => 'Terms & Conditions',
      'prevent-overdue-reminders' => 'Prevent sending overdue reminders for this invoice',
    'prevent-overdue-reminders-order' => 'Prevent sending overdue reminders for this order',
      'allowed-paymodes' => 'Allowed payment modes',
    'sale-agent' => 'Sale agent',
    'client' => 'Client',
    ),
    'selected-customer-address' => 'Selected customer billing address',
    'selected-customer-delivery-address' => 'Selected customer delivery/Shipping address',
    'selected-supplier-delivery-address' => 'Selected supplier delivery/Shipping address',
  ),    

  //end credit notes

  'quotes' => 
  array (
    'title' => 'Quotes',
    'fields' => 
    array (
      'customer' => 'Customer',
      'sale-agent' => 'Sale agent',
      'show-delivery-address' => 'Show delivery details in quotes',
      'status' => 'Status',
      'title' => 'Title',
      'address' => 'Address',
      'quote-prefix' => 'Quote prefix',
      'show-quantity-as' => 'Show quantity as',
      'quote-no' => 'Quote no.',
      'reference' => 'Reference',
      'quote-date' => 'Quote date',
      'quote-expiry-date' => 'Quote expiry date :',
      'proposal-text' => 'Proposal text :',
      'currency' => 'Currency',
      'client-notes' => 'Client notes',
      'tax' => 'Tax',
      'discount' => 'Discount',
      'amount' => 'Amount',
      'created-by' => 'Created by',
      'publish-status' => 'Publish status',
    ),
  ),
  'recurring-invoices' => 
  array (
    'title' => 'Recurring invoices',
    'total-cycles-help' => "'0' means infinity recurring, and this works only when recurring value greater than '0'",
    'recurring-value-help' => "'0' means not recurring",
    'fields' => 
    array (
      'customer' => 'Customer',
      'currency' => 'Currency',
      'title' => 'Title',
      'subject'=>'Subject',
      'address' => 'Address',
      'invoice-prefix' => 'Invoice prefix',
      'show-quantity-as' => 'Show quantity as',
      'invoice-no' => 'Invoice #',
      'status' => 'Status',
      'reference' => 'Reference: ',
      'invoice-date' => 'Invoice date',
      'invoice-due-date' => 'Invoice due date',
      'invoice-notes' => 'Invoice notes',
      'tax' => 'Additional tax',
      'discount' => 'Additional discount',
      'recurring-period' => 'Recurring period',
      'amount' => 'Amount',
      'products' => 'Products',
      'paymentstatus' => 'Payment status',
      'created-by' => 'Created by',
    'total_cycles' => 'Total cycles',
    'recurring_value' => 'Recurring value',
    'recurring_type' => 'Recurring type',
    ),
  ),
  'contact-groups' => 
  array (
    'title' => 'Contact groups',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
    ),
  ),
  'contact-types' => 
  array (
    'title' => 'Contact types',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
    ),
  ),
  'contact-notes' => 
  array (
    'title' => 'Contact notes',
    'fields' => 
    array (
      'title' => 'Title',
      'contact' => 'Contact',
      'notes' => 'Notes',
      'attachment' => 'Attachment',
    ),
  ),
  'contact-documents' => 
  array (
    'title' => 'Contact documents',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
      'attachments' => 'Attachments',
      'contact' => 'Contact',
    ),
  ),
  'products-transfer' => 
  array (
    'title' => 'Products transfer',
  ),
    'product' => 
  array (
    'enter-actual-price' => 'Enter actual price',
    'enter-sale-price' => 'Enter sale price',
  ),
  'products-return' => 
  array (
    'title' => 'Products return',
    'fields' => 
    array (
      'subject' => 'Subject',
      'customer' => 'Customer',
      'currency' => 'Currency',
      'status' => 'Status',
      'address' => 'Address',
      'invoice-prefix' => 'Invoice prefix',
      'show-quantity-as' => 'Show quantity as',
      'invoice-no' => 'Invoice #',
      'reference' => 'Reference',
      'order-date' => 'Order date',
      'order-due-date' => 'Order due date',
      'update-stock' => 'Update stock',
      'notes' => 'Notes',
      'tax' => 'Tax',
      'discount' => 'Discount',
      'ware-house' => 'Ware house',
    ),
  ),
  'brands' => 
  array (
    'title' => 'Brands',
    'fields' => 
    array (
      'title' => 'Title',
      'icon' => 'Icon',
      'status' => 'Status',
    ),
  ),
  'database-backup' => 
  array (
    'title' => 'Database backup',
  ),
  'departments' => 
  array (
    'title' => 'Departments',
    'fields' => 
    array (
      'name' => 'Name',
      'description' => 'Description',
      'created-by' => 'Created by',
    ),
  ),
  'support' => 
  array (
    'title' => 'Support',
    'fields' => 
    array (
      'id'   => '#',
      'name' => 'Name',
      'status' => 'Status',
      'updated_at' => 'Updated at',
      'agent' => 'Agent',
      'user' => 'User',
      'category-by' => 'Category by',
      'email' => 'Email',
      'subject' => 'Subject',
      'department' => 'Department',
      'last-updated' => 'Last updated',
      'owner' => 'Owner',
      'priority' => 'Priority',
      'description' => 'Description',
      'attachments' => 'Attachments',
      'created-by' => 'Created by',
      'category' => 'Category',
      'assigned-to' => 'Assigned to',
    ),
  ),
  'knowledgebase' => 
  array (
    'title' => 'Knowledgebase',
  ),
  'transfers' => 
  array (
    'title' => 'Transfers',
    'fields' => 
    array (
      'from' => 'From',
      'to' => 'To',
      'date' => 'Date',
      'amount' => 'Amount',
      'ref-no' => 'Ref #',
      'payment-method' => 'Payment method',
      'description' => 'Description',
    ),
  ),
  'articles' => 
  array (
    'title' => 'Articles',
    'fields' => 
    array (
      'title' => 'Title',
      'category-id' => 'Categories',
      'tag-id' => 'Tags',
      'page-text' => 'Text',
      'excerpt' => 'Excerpt',
      'featured-image' => 'Featured image',
      'available-for' => 'Available for',
    ),
  ),
  'balance-sheet' => 
  array (
    'title' => 'Balance sheet',
  ),
  'general-settings' => 
  array (
    'title' => 'General settings',
  ),
  'master-settings' => 
  array (
    'title' => 'Master settings',
    'fields' => 
    array (
      'module' => 'Module',
      'key' => 'Key',
      'description' => 'Description',
    ),
  ),
  'countries' => 
  array (
    'title' => 'Countries',
    'fields' => 
    array (
      'dailcode'  => 'Dailcode',
      'shortcode' => 'Shortcode',
      'title' => 'Title',
    ),
  ),
  'measurement-units' => 
  array (
    'title' => 'Measurement units',
    'fields' => 
    array (
      'title' => 'Title',
      'status' => 'Status',
      'description' => 'Description',
    ),
  ),
  'payments' => 
  array (
    'title' => 'Payments',
    'failed' => 'Payment Failed',
    'cancelled' => 'You have cancelled your payment',
  ),

  'quote-tasks' => [
    'title' => 'Quote tasks',
  'recurring-task' => 'Recurring Task',
  'mask-as' => 'Mark as ',
    'fields' => [
      'name' => 'Name',
      'description' => 'Description',
      'task-info'   => 'Task Info',
      'priority' => 'Priority',
      'quote'    => 'Quote id', 
      'startdate' => 'Start Date',
      'duedate' => 'Due Date',
      'datefinished' => 'Date finished',
      'status' => 'Status',
      'recurring' => 'Recurring period',
      'recurring-type' => 'Recurring type',
      'recurring-value' => 'Recurring every',
      'cycles' => 'Cycles',
      'total-cycles' => 'Total cycles',
      'last-recurring-date' => 'Last recurring date',
      'is-public' => 'Is public?',
      'billable' => 'Billable',
      'billed' => 'Billed',
      'invoice' => 'Invoice id',
      'hourly-rate' => 'Hourly rate',
      'milestone' => 'Milestone',
      'kanban-order' => 'Kanban order',
      'milestone-order' => 'Milestone order',
      'visible-to-client' => 'Visible to client?',
      'deadline-notified' => 'Deadline notified?',
      'created-by' => 'Created by',
      'mile-stone' => 'Mile stone',
      'attachments' => 'Attachments',
    ],
  ],

  'quotes-notes' => [
    'title' => 'Quotes notes',
    'fields' => [
      'description' => 'Description',
      'date-contacted' => 'Date contacted',
      'quote' => 'Quote id',
    ],
  ],

  'invoice-tasks' => [
    'title' => 'Invoice tasks',
    'fields' => [
      'name' => 'Name',
      'description' => 'Description',
      'priority' => 'Priority',
      'startdate' => 'Start date',
      'task-info' => 'Invoice task info',
      'duedate' => 'Due date',
      'datefinished' => 'Date finished',
      'status' => 'Status',
      'recurring' => 'Recurring period',
      'recurring-type' => 'Recurring type',
      'recurring-value' => 'Recurring every',
      'cycles' => 'Cycles',
      'total-cycles' => 'Total cycles',
      'last-recurring-date' => 'Last recurring date',
      'is-public' => 'Is public?',
      'billable' => 'Billable',
      'billed' => 'Billed',
      'invoice' => 'Invoice id',
      'hourly-rate' => 'Hourly rate',
      'kanban-order' => 'Kanban order',
      'milestone-order' => 'Milestone order',
      'visible-to-client' => 'Visible to client?',
      'deadline-notified' => 'Deadline notified?',
      'created-by' => 'Created by',
      'mile-stone' => 'Mile stone',
      'attachments' => 'Attachments',
      'assigned-to' => 'Assigned to',
    ],
  ],
  
  'invoice-reminders' => [
    'title' => 'Invoice reminders',
    'fields' => [
      'description' => 'Description',
      'date' => 'Date',
      'isnotified' => 'Is notified?',
      'invoice' => 'Invoice id',
      'reminder-to' => 'Reminder to',
      'notify-by-email' => 'Notify by email',
      'created-by' => 'Created by',
    ],
  ],
  
  'invoice-notes' => [
    'title' => 'Invoice notes',
    'fields' => [
      'description' => 'Description',
      'date-contacted' => 'Date contacted',
      'quote' => 'Quote id',
      'created-by' => 'Created by',
    ],
  ],

  'project-tasks' => [
    'title' => 'Project tasks',
  'completed-by' => 'Completed by',
  'task-not-completed-only-htlp' => 'It will show not completed tasks only',
  'completed-by-htlp' => 'Shows project assignees only',
  'timer' => 'Timer',
  'total-logged-time' => 'Total logged time :hours',
  'no-timers' => 'No timers found for this task',
  'running-timer' => 'Timer is running on this task',
    'fields' => [
      'name' => 'Name',
      'description' => 'Description',
      'priority' => 'Priority',
      'startdate' => 'Start date',
      'duedate' => 'Due date',
      'datefinished' => 'Date finished',
      'status' => 'Status',
      'recurring' => 'Recurring period',
      'recurring-type' => 'Recurring type',
      'recurring-value' => 'Recurring every',
      'cycles' => 'Cycles',
      'total-cycles' => 'Total cycles',
      'last-recurring-date' => 'Last recurring date',
      'is-public' => 'Is public?',
      'billable' => 'Billable',
      'billed' => 'Billed',
      'project' => 'Project',
      'hourly-rate' => 'Hourly rate',
      'milestone' => 'Milestone',
      'kanban-order' => 'Kanban order',
      'milestone-order' => 'Milestone order',
      'visible-to-client' => 'Visible to client?',
      'deadline-notified' => 'Deadline notified?',
      'mile-stone' => 'Mile stone',
      'attachments' => 'Attachments',
      'created-by' => 'Created by',
    ],
  ],
  
  'navigation-menues' => 
  array (
    'title' => 'Navigation Menu',
  ),

  'app_create' => 'Create',
  'app_save' => 'Save',
  'app_reset' => 'Reset',
  'app_save_send' => 'Save & Send',
  'app_edit' => 'Edit',
  'app_restore' => 'Restore',
  'app_cancel' => 'Cancel',
  'app_close' => 'Close',
  'app_values' => 'Values',
  'app_permadel' => 'Delete permanently',
  'app_all' => 'All',
  'app_trash' => 'Trash',
  'app_view' => 'View',
  'app_view_file' => 'View file',
  'app_update' => 'Update',
  'app_list' => 'List',
  'app_no_entries_in_table' => 'No entries in table',
  'app_custom_controller_index' => 'Custom controller index.',
  'app_logout' => 'Logout',
  'app_add_new' => 'Add new',
  'app_add_new_contract_type'=>'Contract type',
  'app_are_you_sure' => 'Are you sure?',
  'app_back_to_list' => 'Back to list',
  'app_dashboard' => 'Dashboard',
  'app_delete' => 'Delete',
  'app_delete_selected' => 'Delete selected',
  'app_category' => 'Category',
  'app_categories' => 'Categories',
  'app_sample_category' => 'Sample category',
  'app_questions' => 'Questions',
  'app_question' => 'Question',
  'app_answer' => 'Answer',
  'app_sample_question' => 'Sample question',
  'app_sample_answer' => 'Sample answer',
  'app_faq_management' => 'FAQ management',
  'app_administrator_can_create_other_users' => 'Administrator (can create other users)',
  'app_simple_user' => 'Simple user',
  'app_title' => 'Title',
  'app_roles' => 'Roles',
  'app_role' => 'Role',
  'app_user_management' => 'User management',
  'app_users' => 'Users',
  'app_user' => 'User',
  'app_name' => 'Name',
  'app_email' => 'Email',
  'app_password' => 'Password',
  'app_remember_token' => 'Remember token',
  'app_permissions' => 'Permissions',
  'app_user_actions' => 'User actions',
  'app_action' => 'Action',
  'app_action_model' => 'Action model',
  'app_action_id' => 'Action id',
  'app_time' => 'Time',
  'app_campaign' => 'Campaign',
  'app_campaigns' => 'Campaigns',
  'app_description' => 'Description',
  'app_valid_from' => 'Valid from',
  'app_valid_to' => 'Valid to',
  'app_discount_amount' => 'Discount amount',
  'app_discount_percent' => 'Discount percent',
  'app_coupons_amount' => 'Coupons amount',
  'app_coupons' => 'Coupons',
  'app_code' => 'Code',
  'app_redeem_time' => 'Redeem time',
  'app_coupon_management' => 'Coupon Management',
  'app_time_management' => 'Time management',
  'app_projects' => 'Projects',
  'app_reports' => 'Reports',
  'app_time_entries' => 'Time entries',
  'app_work_type' => 'Work type',
  'app_work_types' => 'Work types',
  'app_project' => 'Project',
  'app_start_time' => 'Start time',
  'app_end_time' => 'End time',
  'app_expense_category' => 'Expense category',
  'app_expense_categories' => 'Expense categories',
  'app_expense_management' => 'Expense management',
  'app_expenses' => 'Expenses',
  'app_expense' => 'Expense',
  'app_entry_date' => 'Entry date',
  'app_amount' => 'Amount',
  'app_income_categories' => 'Income categories',
  'app_monthly_report' => 'Monthly report',
  'app_companies' => 'Companies',
  'app_company_name' => 'Company name',
  'app_address' => 'Address',
  'app_website' => 'Website',
  'app_contact_management' => 'Contact management',
  'app_contacts' => 'Contacts',
  'app_company' => 'Company',
  'app_first_name' => 'First name',
  'app_last_name' => 'Last name',
  'app_phone' => 'Phone',
  'app_phone1' => 'Phone 1',
  'app_phone2' => 'Phone 2',
  'app_skype' => 'Skype',
  'app_photo' => 'Photo (max 8mb)',
  'app_category_name' => 'Category name',
  'app_product_management' => 'Product management',
  'app_products' => 'Products',
  'app_product_name' => 'Product name',
  'app_price' => 'Price',
  'app_tags' => 'Tags',
  'app_tag' => 'Tag',
  'app_photo1' => 'Photo1',
  'app_photo2' => 'Photo2',
  'app_photo3' => 'Photo3',
  'app_calendar' => 'Calendar',
  'app_statuses' => 'Statuses',
  'app_task_management' => 'Task management',
  'app_tasks' => 'Tasks',
  'app_task' => 'Task',
  'app_status' => 'Status',
  'app_attachment' => 'Attachment',
  'app_due_date' => 'Due date',
  'app_assigned_to' => 'Assigned to',
  'app_assets' => 'Assets',
  'app_asset' => 'Asset',
  'app_serial_number' => 'Serial number',
  'app_location' => 'Location',
  'app_locations' => 'Locations',
  'app_assigned_user' => 'Assigned (user)',
  'app_notes' => 'Notes',
  'app_assets_history' => 'Assets history',
  'app_assets_management' => 'Assets management',
  'app_slug' => 'Slug',
  'app_content_management' => 'Content management',
  'app_text' => 'Text',
  'app_excerpt' => 'Excerpt',
  'app_featured_image' => 'Featured image',
  'app_pages' => 'Pages',
  'app_axis' => 'Axis',
  'app_show' => 'Show',
  'app_group_by' => 'Group by',
  'app_chart_type' => 'Chart type',
  'app_create_new_report' => 'Create new report',
  'app_no_reports_yet' => 'No reports yet.',
  'app_created_at' => 'Created at',
  'app_updated_at' => 'Updated at',
  'app_deleted_at' => 'Deleted at',
  'app_reports_x_axis_field' => 'X-axis - please choose one of date/time fields',
  'app_reports_y_axis_field' => 'Y-axis - please choose one of number fields',
  'app_select_crud_placeholder' => 'Please select one of your CRUDs',
  'app_select_dt_placeholder' => 'Please select one of date/time fields',
  'app_aggregate_function_use' => 'Aggregate function to use',
  'app_x_axis_group_by' => 'X-axis group by',
  'app_x_axis_field' => 'X-axis field (date/time)',
  'app_y_axis_field' => 'Y-axis field',
  'app_integer_float_placeholder' => 'Please select one of integer/float fields',
  'app_change_notifications_field_1_label' => 'Send email notification to User',
  'app_change_notifications_field_2_label' => 'When Entry on CRUD',
  'app_select_users_placeholder' => 'Please select one of your Users',
  'app_is_created' => 'is created',
  'app_is_updated' => 'is updated',
  'app_is_deleted' => 'is deleted',
  'app_notifications' => 'Notifications',
  'app_notify_user' => 'Notify User',
  'app_when_crud' => 'When CRUD',
  'app_create_new_notification' => 'Create new notification',
  'app_stripe_transactions' => 'Stripe transactions',
  'app_upgrade_to_premium' => 'Upgrade to premium',
  'app_messages' => 'Messages',
  'app_you_have_no_messages' => 'You have no messages.',
  'app_all_messages' => 'All messages',
  'app_new_message' => 'New message',
  'app_outbox' => 'Outbox',
  'app_inbox' => 'Inbox',
  'app_recipient' => 'Recipient',
  'app_subject' => 'Subject',
  'app_message' => 'Message',
  'app_send' => 'Send',
  'app_reply' => 'Reply',
  'app_calendar_sources' => 'Calendar sources',
  'app_new_calendar_source' => 'Create new calendar source',
  'app_crud_title' => 'Crud title',
  'app_crud_date_field' => 'Crud date field',
  'app_prefix' => 'Prefix',
  'app_label_field' => 'Label field',
  'app_suffix' => 'Sufix',
  'app_no_calendar_sources' => 'No calendar sources yet.',
  'app_crud_event_field' => 'Event label field',
  'app_create_new_calendar_source' => 'Create new calendar source',
  'app_edit_calendar_source' => 'Edit calendar source',
  'app_client_management' => 'Client management',
  'app_client_management_settings' => 'Client management settings',
  'app_country' => 'Country',
  'app_client_status' => 'Client status',
  'app_clients' => 'Clients',
  'app_client_statuses' => 'Client statuses',
  'app_currencies' => 'Currencies',
  'app_main_currency' => 'Main currency',
  'app_documents' => 'Documents',
  'app_file' => 'File',
  'app_income_source' => 'Income source',
  'app_income_sources' => 'Income sources',
  'app_fee_percent' => 'Fee percent',
  'app_note_text' => 'Note text',
  'app_client' => 'Client',
  'app_start_date' => 'Start date',
  'app_budget' => 'Budget',
  'app_project_status' => 'Project status',
  'app_project_statuses' => 'Project statuses',
  'app_transactions' => 'Transactions',
  'app_transaction_types' => 'Transaction types',
  'app_transaction_type' => 'Transaction type',
  'app_transaction_date' => 'Transaction date',
  'app_currency' => 'Currency',
  'app_current_password' => 'Current password',
  'app_new_password' => 'New password',
  'app_password_confirm' => 'New password confirmation',
  'app_dashboard_text' => 'You are logged in!',
  'app_forgot_password' => 'Forgot your password?',
  'app_remember_me' => 'Remember me',
  'app_login' => 'Login',
  'app_change_password' => 'Change password',
  'app_csv' => 'CSV',
  'app_print' => 'Print',
  'app_excel' => 'Excel',
  'app_copy' => 'Copy',
  'app_colvis' => 'Column visibility',
  'app_pdf' => 'PDF',
  'app_reset_password' => 'Reset password',
  'app_reset_password_woops' => '<strong>Whoops!</strong> There were problems with input:',
  'app_email_line1' => 'You are receiving this email because we received a password reset request for your account.',
  'app_email_line2' => 'If you did not request a password reset, no further action is required.',
  'app_email_greet' => 'Hello',
  'app_email_regards' => 'Regards',
  'app_confirm_password' => 'Confirm password',
  'app_if_you_are_having_trouble' => 'If you’re having trouble clicking the',
  'app_copy_paste_url_bellow' => 'button, copy and paste the URL below into your web browser:',
  'app_please_select' => 'Please select',
  'app_register' => 'Register',
  'app_registration' => 'Registration',
  'app_not_approved_title' => 'You are not approved',
  'app_not_approved_p' => 'Your account is still not approved by administrator. Please, be patient and try again later.',
  'app_there_were_problems_with_input' => 'There were problems with input',
  'app_whoops' => 'Whoops!',
  'app_file_contains_header_row' => 'File contains header row?',
  'app_csvImport' => 'CSV Import',
  'app_csv_file_to_import' => 'CSV file to import',
  'app_parse_csv' => 'Parse CSV',
  'app_import_data' => 'Import data',
  'app_imported_rows_to_table' => 'Imported :rows rows to :table table',
  'app_subscription-billing' => 'Subscriptions',
  'app_subscription-payments' => 'Payments',
  'app_basic_crm' => 'Basic CRM',
  'app_customers' => 'Customers',
  'app_customer' => 'Customer',
  'app_select_all' => 'Select all',
  'app_deselect_all' => 'Deselect all',
  'app_team-management' => 'Teams',
  'app_team-management-singular' => 'Team',
  'global_title' => 'Digi Accounting, Invoicing & Billing CRM',
  'app_add_key' => 'Add Key',
  'app_settings' => 'Settings',
  'app_make_default' => 'Make default',
  'info' => 'Info',
  'operations_disabled' => 'Operation disabled on demo mode',
  'download-template' => 'Download Template',
  'app_refresh' => 'Refresh',
  'app_loading' => 'Loading',
  'make_default' => 'Make default',
  'ltr' => 'LTR',
  'rtl' => 'RTL',
  'make-ltr' => 'Make LRT',
  'make-rtl' => 'Make RTL',
  'add_new_title' => 'Add new :title',
  'app_send_email' => 'Send email',
  'app_change_order' => 'Change order',
  'app_create_user' => 'Create user',
  'mile-stones' => [
    'title' => 'Mile stones',
    'fields' => [
      'name' => 'Name',
      'description' => 'Description',
      'description-visible-to-customer' => 'Description visible to customer?',
      'due-date' => 'Due date',
      'project' => 'Project',
      'color' => 'Color',
      'milestone-order' => 'Milestone order',
    ],
  ],
  
  'quotes-reminders' => [
    'title' => 'Quotes reminders',
    'fields' => [
      'description' => 'Description',
      'date' => 'Date',
      'isnotified' => 'Is notified?',
      'quote' => 'Quote id',
      'reminder-to' => 'Reminder to',
      'notify-by-email' => 'Notify by email',
      'created-by' => 'Created by',
    ],
  ],
  
  'project-discussions' => [
    'title' => 'Discussions',
    'total-comments' => 'Total comments',
    'comment' => 'Send comment',
    'comments' => 'Comments',
    'attachment' => 'Attachment',
    'posted-on' => 'Posted on :date',
    'posted-by' => 'Posted by :name',
    'comment-posted' => 'Comment posted successfully',
    'comment-answered' => 'Comment answered successfully',
    'comment-updated' => 'Comment updated successfully',
    'add-comment' => 'Add comment',
    'answer' => 'Reply',
    'fields' => [
      'subject' => 'Subject',
      'description' => 'Description',
      'visible-to-customer' => 'Visible to client?',
      'last-activity' => 'Last activity',
      'project' => 'Project',
    ],
  ],

  'dashboard-widgets' => [
    'title' => 'Dashboard widgets',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'numbers' => 'Numbers',
    'chart' => 'Chart',
    'list' => 'List',
    'no-widgets' => 'No widgets for this role',
    'assign-widgets' => 'Assign widgets',
    'role' => 'Role',
    'fields' => [
      'title' => 'Title',
      'status' => 'Status',
      'role' => 'Role',
      'order' => 'Order',
      'type' => 'Type',
      'slug' => 'Slug',
      'columns' => 'Columns',
    ]
  ],

  'proposals' => 
  array (
    'title' => 'Proposals',
    'fields' => 
    array (
      'customer' => 'Customer',
      'sale-agent' => 'Sale agent',
      'show-delivery-address' => 'Show delivery details in proposals',
      'status' => 'Status',
      'title' => 'Title',
      'subject'=>'Subject',
      'address' => 'Address',
      'proposal-prefix' => 'Proposal prefix',
      'show-quantity-as' => 'Show quantity as',
      'proposal-no' => 'Proposal no.#',
      'reference' => 'Reference',
      'proposal-date' => 'Proposal date',
      'proposal-expiry-date' => 'Proposal expiry date',
      'proposal-text' => 'Proposal text :',
      'currency' => 'Currency',
      'client-notes' => 'Client notes',
      'tax' => 'Tax',
      'discount' => 'Discount',
      'amount' => 'Amount',
      'created-by' => 'Created by',
      'publish-status' => 'Publish status',
    ),
  ),

  'proposals-reminders'=>
  array(
      'title'=>'Proposal reminders',
      'fields'=>
      array(
        'date'=>'Date',
        'isnotified'=>'Isnotified?',
        'reminder-to'=>'Reminder to',
        'notify-by-email'=>'Notify by email',
        'description'=>'Description',
        'proposal'=>'Proposal no.',
        'created-by'=>'Created by',
      ),
    ),
  'proposal-reminders'=>
  array(
    'title'=>'Proposals reminders',
  ),

  'proposal-tasks'=>
  array(
        'title'=>'Proposal tasks',
        'fields'=>
        array(
            'name'=>'Name',
            'startdate'=>'Start Date',
            'duedate'=>'Due Date',
            'status'=>'Status',
            'task-info'=>'Task Info',
            'datefinished'=>'Date finished',
            'last-recurring-date'=>'Last recurring date',
            'billed'=>'Billable',
            'proposal'=>'Proposal id',
            'deadline-notified'=>'Deadline notified?',
            'created-by'=>'Created by',
            'priority'=>'Priority',
            'billable'=>'Billable',
            'recurring'=>'Recurring',
            'recurring-type'=>'Recurring type',
            'recurring-value'=>'Recurring value',
            'cycles'=>'Cycles',
            'visible-to-client'=>'Visible to client',
            'attachments'=>'Attachments',
            'description'=>'Description',
          ),
      ),
  'proposals-notes'=>
  array(
        'title'=> 'Proposals Notes',
        'fields'=>
        array(
            'description'=>'Description',
          ),
      ),
  'proposal-notes'=>
  array(
      'title'=>'Proposal notes',
  ),

'contracts' => 
  array (
    'title' => 'Contracts',
    'fields' => 
    array (
      'customer' => 'Customer',
      'sale-agent' => 'Sale agent',
      'show-delivery-address' => 'Show delivery details in contracts',
      'show-delivery-address-contract'=> 'Show delivery details in contracts',
      'status' => 'Status',
      'title' => 'Title',
      'address' => 'Address',
      'contract-prefix' => 'Contract prefix',
      'show-quantity-as' => 'Show quantity as',
      'contract-no' => 'Contract no.#',
      'reference' => 'Reference',
      'contract-date' => 'Contract date',
      'contract-expiry-date' => 'Contract expiry date',
      'contract_value' => 'Contract value',
      'contract_type' => 'Contract type',
      'visible-to-customer'=>'Visible to customer',
      'contract-text' => 'Contract text :',
      'currency' => 'Currency',
      'client-notes' => 'Client notes',
      'tax' => 'Tax',
      'discount' => 'Discount',
      'amount' => 'Amount',
      'created-by' => 'Created by',
      'publish-status' => 'Publish status',
    ),
  ),

 

  'contract-tasks' => [
    'title' => 'Contract Tasks',
  'recurring-task' => 'Recurring Task',
  'mask-as' => 'Mark as ',
    'fields' => [
      'name' => 'Name',
      'description' => 'Description',
      'task-info'   => 'Task Info',
      'priority' => 'Priority',
      'contract'    => 'Contract id', 
      'startdate' => 'Start Date',
      'duedate' => 'Due Date',
      'datefinished' => 'Date finished',
      'status' => 'Status',
      'recurring' => 'Recurring period',
      'recurring-type' => 'Recurring type',
      'recurring-value' => 'Recurring every',
      'cycles' => 'Cycles',
      'total-cycles' => 'Total cycles',
      'last-recurring-date' => 'Last recurring date',
      'is-public' => 'Is public?',
      'billable' => 'Billable',
      'billed' => 'Billed',
      'invoice' => 'Invoice id',
      'hourly-rate' => 'Hourly rate',
      'milestone' => 'Milestone',
      'kanban-order' => 'Kanban order',
      'milestone-order' => 'Milestone order',
      'visible-to-client' => 'Visible to client?',
      'deadline-notified' => 'Deadline notified?',
      'created-by' => 'Created by',
      'mile-stone' => 'Mile stone',
      'attachments' => 'Attachments',
      'related_to' => 'Related To',
    ],
  ],
  'contracts-reminders' => [
    'title' => 'Contracts reminders',
    'fields' => [
      'description' => 'Description',
      'date' => 'Date',
      'isnotified' => 'Is notified?',
      'contract' => 'Contract id',
      'reminder-to' => 'Reminder to',
      'notify-by-email' => 'Notify by email',
      'created-by' => 'Created by',
    ],
  ],


  'contracts-notes' => [
    'title' => 'Contracts notes',
    'fields' => [
      'description' => 'Description',
      'date-contacted' => 'Date contacted',
      'contract' => 'Contract id',
    ],
  ],
  'contract-notes'=>
  array(
      'title'=>'Contract notes',
  ),

  
  'contract_types' => 
  array (
    'title' => 'ContractTypes',
    'fields' => 
    array (
      'name' => 'Type name',
      'description' => 'Description',
      
    ),
  ),
  'app_default' => 'Make default',
  'cancel' => 'Cancel',
  'app_edit_user'=>'Edit user',

);
