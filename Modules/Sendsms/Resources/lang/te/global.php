<?php

return array (
  'sms-gateways' => 
  array (
    'title' => 'SMS ముఖద్వారాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'key' => 'కీ',
      'description' => 'వివరణ',
    ),
  ),
  'send-sms' => 
  array (
    'title' => 'SMS పంపండి',
    'no-gateway' => 'డిఫాల్ట్ గేట్ వే ఎంచుకోబడలేదు. దయచేసి సెట్టింగులు పేజీ నుండి డిఫాల్ట్ గేట్వే ఎంచుకోండి',
    'sent-success' => 'SMS పంపింది విజయవంతంగా',
    'dont-have-phone' => 'క్షమించండి ఈ క్లయింట్ ఫోన్ నంబర్ లేదు, దయచేసి పరిచయాలలో జోడించండి.',
    'dont-have-phone-edit' => 'క్షమించండి ఈ క్లయింట్ ఫోన్ నంబర్ లేదు, సవరించడానికి <a href=":url">ఇక్కడ</a> క్లిక్ <a href=":url">చేయండి</a> .',
    'status-changed' => 'స్థితి విజయవంతంగా మార్చబడింది',
    'fields' => 
    array (
      'send-to' => 'పంపే',
      'message' => 'సందేశం',
      'gateway' => 'గేట్వే',
    ),
  ),
);
