<?php

return array (
  'sms-gateways' => 
  array (
    'title' => 'SMS gateways',
    'fields' => 
    array (
      'name' => 'Name',
      'key' => 'Key',
      'description' => 'Description',
    ),
  ),
  'send-sms' => 
  array (
    'title' => 'Send SMS',
	'send' => 'Send',
	're-send' => 'Re-send',
    'no-gateway' => 'There is not default gateway selected. Please select default gateway from settings page',
    'sent-success' => 'SMS Sent successfully',
    'dont-have-phone' => 'Sorry this client dont have phone number, please add in contacts.',
    'dont-have-phone-edit' => 'Sorry this client dont have phone number, click <a href=":url">here</a> to edit.',
    'status-changed' => 'Status changed successfully',
    'fields' => 
    array (
      'send-to' => 'Send to',
      'message' => 'Message',
      'gateway' => 'Gateway',
    ),
  ),
);
