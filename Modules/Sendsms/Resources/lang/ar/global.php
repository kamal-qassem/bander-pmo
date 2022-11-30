<?php

return array (
  'sms-gateways' => 
  array (
    'title' => 'بوابات الرسائل القصيرة',
    'fields' => 
    array (
      'name' => 'اسم',
      'key' => 'مفتاح',
      'description' => 'وصف',
    ),
  ),
  'send-sms' => 
  array (
    'title' => 'أرسل رسالة نصية قصيرة',
    'no-gateway' => 'لا توجد بوابة افتراضية محددة. يرجى اختيار العبارة الافتراضية من صفحة الإعدادات',
    'sent-success' => 'تم إرسال الرسائل القصيرة بنجاح',
    'dont-have-phone' => 'آسف هذا العميل لا تملك رقم الهاتف ، يرجى إضافة جهات الاتصال.',
    'dont-have-phone-edit' => 'آسف هذا العميل لا يملك رقم الهاتف ، انقر <a href=":url">هنا</a> للتعديل.',
    'status-changed' => 'تم تغيير الحالة بنجاح',
    'fields' => 
    array (
      'send-to' => 'ارسل إلى',
      'message' => 'رسالة',
      'gateway' => 'بوابة',
    ),
  ),
);
