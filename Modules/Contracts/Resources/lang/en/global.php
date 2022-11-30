<?php

return array (
  'contracts' => 
  array (
    'title' => 'Contracts',
    'fields' => 
    array (
      'customer' => 'Customer',
      'sale-agent' => 'Sale agent',
      'show-delivery-address' => 'Show delivery details in contracts',
      'status' => 'Status',
      'title' => 'Title',
      'address' => 'Address',
      'contract-prefix' => 'Contract prefix',
      'show-quantity-as' => 'Show quantity as',
      'contract-no' => 'Contract no.#',
      'reference' => 'Reference',
      'contract-date' => 'Contract date',
      'contract-expiry-date' => 'Contract expiry date',
      'contract_value' => 'Contract Value',
      'contract_type' => 'Contract Type',
      'visible-to-customer'=>'Visible to Customer',
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
  
  'contract_types' => 
  array (
    'title' => 'ContractTypes',
    'fields' => 
    array (
      'name' => 'Type name',
      'description' => 'Description',
      
    ),
  ),
  'invoices'=>
  array(
  	'fields'=>
  	array(
  	'contract-date'=>'Contract date',
      'contract-expiry-date'=>'Contract expiry date',
  ),
 ),

  'app_default' => 'Make default',
  'cancel' => 'Cancel',

);
