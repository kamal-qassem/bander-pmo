<?php

return array (
  'user-management' => 
  array (
    'title' => 'إدارةالمستخدم',
  ),
  'permissions' => 
  array (
    'title' => 'أذونات',
    'fields' => 
    array (
      'title' => 'عنوان',
    ),
  ),
  'roles' => 
  array (
    'title' => 'الأدوار',
    'fields' => 
    array (
      'title' => 'عنوان',
      'permission' => 'أذونات',
    ),
  ),
  'users' => 
  array (
    'title' => 'المستخدمين',
    'active' => 'نشيط',
    'suspend' => 'تعليق',
    'activate' => 'تفعيل',
    'fields' => 
    array (
      'name' => 'اسم',
      'email' => 'البريد الإلكتروني',
      'password' => 'كلمه السر',
      'role' => 'وظيفة',
      'remember-token' => 'تذكر الرمز',
      'contact-reference' => 'مرجع الاتصال',
      'department' => ' قسم، أقسام',
      'status' => 'الحالة',
    ),
  ),
  'user-actions' => 
  array (
    'title' => 'إجراءات المستخدم',
    'created_at' => 'زمن',
    'fields' => 
    array (
      'user' => 'المستعمل',
      'action' => 'عمل',
      'action-model' => 'نموذج العمل',
      'action-id' => 'معرف العمل',
    ),
  ),
  'contact-management' => 
  array (
    'title' => 'إدارة الاتصال',
  ),
  'contact-companies' => 
  array (
    'title' => 'الشركات',
    'fields' => 
    array (
      'name' => 'اسم الشركة',
      'email' => 'البريد الإلكتروني',
      'address' => 'عنوان',
      'website' => 'موقع الكتروني',
    ),
  ),
  'contacts' => 
  array (
    'title' => 'جهات الاتصال',
    'title_customers' => 'الزبائن',
    'title_customer' => 'زبون',
    'title_suppliers' => 'الموردين',
    'title_supplier' => 'المورد',
    'title_leads' => 'يؤدي',
    'title_lead' => 'قيادة',
    'title_profile' => 'الملف الشخصي',
    'edit-profile' => 'تعديل الملف الشخصي',
    'fields' => 
    array (
      'company' => 'شركة',
      'group' => 'مجموعة',
      'contact-type' => 'نوع الاتصال',
      'first-name' => 'الاسم الاول',
      'last-name' => 'الكنية',
      'language' => 'لغة',
      'phone1' => 'الهاتف 1',
      'phone2' => 'الهاتف 2',
      'phone1_code' => 'رقم الهاتف 1',
      'phone2_code' => 'رمز الهاتف 2',
      'email' => 'البريد الإلكتروني',
      'skype' => 'سكايب',
      'address' => 'عنوان',
      'city' => 'مدينة',
      'state-region' => 'الدولة / المنطقة',
      'zip-postal-code' => 'الرمز البريدي / الرمز البريدي',
      'tax-id' => 'الرقم الضريبي',
      'country' => 'بلد',
    ),
  ),
  'expense-management' => 
  array (
    'title' => 'محاسبة',
  ),
  'expense-category' => 
  array (
    'title' => 'فئات النفقات',
    'fields' => 
    array (
      'name' => 'اسم',
    ),
  ),
  'income-category' => 
  array (
    'title' => 'فئات الدخل',
    'fields' => 
    array (
      'name' => 'اسم',
    ),
  ),
  'income' => 
  array (
    'title' => 'الإيرادات',
    'fields' => 
    array (
      'account' => 'الحساب',
      'income-category' => 'فئة الدخل',
      'entry-date' => 'موعد الدخول',
      'amount' => 'كمية',
      'description' => 'وصف',
      'description-file' => 'ملف الوصف',
      'payer' => 'دافع',
      'pay-method' => 'طريقة الدفع',
      'ref-no' => 'الرقم المرجعي',
    ),
  ),
  'expense' => 
  array (
    'title' => 'نفقات',
    'fields' => 
    array (
      'account' => 'الحساب',
      'expense-category' => 'فئة النفقات',
      'entry-date' => 'موعد الدخول',
      'amount' => 'كمية',
      'description' => 'وصف',
      'description-file' => 'ملف الوصف',
      'payee' => 'المستفيد',
      'payment-method' => 'طريقة الدفع او السداد',
      'ref-no' => 'الرقم المرجعي',
    ),
  ),
  'monthly-report' => 
  array (
    'title' => 'التقرير الشهري',
  ),
  'faq-management' => 
  array (
    'title' => 'إدارة التعليمات',
    'faq' => 'أسئلة وأجوبة',
  ),
  'faq-categories' => 
  array (
    'title' => 'الاقسام',
    'fields' => 
    array (
      'title' => 'الفئة',
    ),
  ),
  'faq-questions' => 
  array (
    'title' => 'الأسئلة',
    'fields' => 
    array (
      'category' => 'الفئة',
      'question-text' => 'سؤال',
      'answer-text' => 'إجابة',
    ),
  ),
  'internal-notifications' => 
  array (
    'title' => 'إخطارات',
    'fields' => 
    array (
      'text' => 'نص',
      'link' => 'حلقة الوصل',
      'users' => 'المستخدمين',
    ),
  ),
  'task-management' => 
  array (
    'title' => 'ادارة المهام',
  ),
  'task-statuses' => 
  array (
    'title' => 'الأوضاع',
    'panel-default' => 'اللوحة الافتراضية',
    'panel-primary' => 'لوحة الأولية',
    'panel-success' => 'لوحة النجاح',
    'panel-info' => 'لوحة المعلومات',
    'panel-warning' => 'لوحة تحذير',
    'panel-danger' => 'لوحة الخطر',
    'fields' => 
    array (
      'name' => 'اسم',
      'color' => 'اللون',
    ),
  ),
  'task-tags' => 
  array (
    'title' => 'الكلمات',
    'fields' => 
    array (
      'name' => 'اسم',
    ),
  ),
  'tasks' => 
  array (
    'title' => 'مهام',
    'task-status-updated' => 'تم تحديث حالة المهمة بنجاح',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
      'status' => 'الحالة',
      'tag' => 'الكلمات',
      'attachment' => 'المرفق',
      'start-date' => 'تاريخ البدء',
      'due-date' => 'تاريخ الاستحقاق',
      'user' => 'مخصص ل',
    ),
  ),
  'task-calendar' => 
  array (
    'title' => 'التقويم',
    'status-wise' => 'الوضع الحكيم',
  ),
  'content-management' => 
  array (
    'title' => 'ادارة المحتوى',
  ),
  'content-categories' => 
  array (
    'title' => 'الاقسام',
    'fields' => 
    array (
      'title' => 'الفئة',
      'slug' => 'سبيكة',
    ),
  ),
  'content-tags' => 
  array (
    'title' => 'الكلمات',
    'fields' => 
    array (
      'title' => 'بطاقة',
      'slug' => 'سبيكة',
    ),
  ),
  'content-pages' => 
  array (
    'title' => 'صفحات',
    'fields' => 
    array (
      'title' => 'عنوان',
      'category-id' => 'الاقسام',
      'tag-id' => 'الكلمات',
      'page-text' => 'نص',
      'excerpt' => 'مقتطفات',
      'featured-image' => 'صورة مميزة',
      'created-at' => 'الوقت خلق',
    ),
  ),
  'product-management' => 
  array (
    'title' => 'ادارة المنتج',
  ),
  'product-categories' => 
  array (
    'title' => 'الاقسام',
    'fields' => 
    array (
      'name' => 'اسم التصنيف',
      'description' => 'وصف',
      'photo' => 'الصورة (بحد أقصى 8 ميجابايت)',
    ),
  ),
  'product-tags' => 
  array (
    'title' => 'الكلمات',
    'fields' => 
    array (
      'name' => 'اسم',
    ),
  ),
  'products' => 
  array (
    'title' => 'منتجات',
    'gallery-file-types' => 'أنواع الملفات المقبولة: png، jpg، jpeg، gif',
    'fields' => 
    array (
      'name' => 'اسم المنتج',
      'product-code' => 'كود المنتج',
      'actual-price' => 'السعر الفعلي',
      'sale-price' => 'سعر البيع',
      'category' => 'الفئة',
      'tag' => 'بطاقة',
      'ware-house' => 'وير المنزل',
      'description' => 'وصف',
      'excerpt' => 'مقتطفات',
      'stock-quantity' => 'كمية المخزون',
      'alert-quantity' => 'تنبيه الكمية',
      'image-gallery' => 'معرض الصور',
      'thumbnail' => 'صورة مصغرة',
      'other-files' => 'ملفات اخرى',
      'hsn-sac-code' => 'رمز HSN / SAC',
      'product-size' => 'حجم المنتج',
      'product-weight' => 'وزن المنتج',
      'brand' => 'علامة تجارية',
    ),
  ),
  'assets-management' => 
  array (
    'title' => 'إدارة الأصول',
  ),
  'assets-categories' => 
  array (
    'title' => 'الاقسام',
    'fields' => 
    array (
      'title' => 'عنوان',
    ),
  ),
  'assets-statuses' => 
  array (
    'title' => 'الأوضاع',
    'fields' => 
    array (
      'title' => 'عنوان',
    ),
  ),
  'assets-locations' => 
  array (
    'title' => 'مواقع',
    'fields' => 
    array (
      'title' => 'عنوان',
    ),
  ),
  'assets' => 
  array (
    'title' => 'الأصول',
    'fields' => 
    array (
      'category' => 'الفئة',
      'serial-number' => 'رقم سري',
      'title' => 'عنوان',
      'photo1' => 'صورة مصغرة',
      'photo2' => 'صالة عرض',
      'attachments' => 'مرفقات',
      'status' => 'الحالة',
      'location' => 'موقعك',
      'assigned-user' => 'معين (مستخدم)',
      'notes' => 'ملاحظات',
    ),
  ),
  'assets-history' => 
  array (
    'title' => 'تاريخ الأصول',
    'created_at' => 'زمن',
    'fields' => 
    array (
      'asset' => 'الأصول',
      'status' => 'الحالة',
      'location' => 'موقعك',
      'assigned-user' => 'معين (مستخدم)',
    ),
  ),
  'coupon-management' => 
  array (
    'title' => 'إدارة القسيمة',
  ),
  'coupon-campaigns' => 
  array (
    'title' => 'حملات',
    'fields' => 
    array (
      'title' => 'عنوان',
      'description' => 'وصف',
      'valid-from' => 'صالح من تاريخ',
      'valid-to' => 'صالحة ل',
      'discount-amount' => 'مقدار الخصم',
      'discount-percent' => 'خصم في المئة',
      'coupons-amount' => 'كوبونات المبلغ',
    ),
  ),
  'coupons' => 
  array (
    'title' => 'كوبونات',
    'fields' => 
    array (
      'campaign' => 'حملة',
      'code' => 'الشفرة',
      'valid-from' => 'صالح من تاريخ',
      'valid-to' => 'صالحة ل',
      'discount-amount' => 'مقدار الخصم',
      'discount-percent' => 'خصم في المئة',
      'redeem-time' => 'استرداد الوقت',
    ),
  ),
  'expense-types' => 
  array (
    'title' => 'أنواع النفقات',
  ),
  'global-settings' => 
  array (
    'title' => 'الاعدادات العامة',
  ),
  'currencies' => 
  array (
    'title' => 'العملات',
    'fields' => 
    array (
      'name' => 'اسم',
      'symbol' => 'رمز',
      'code' => 'الشفرة',
      'rate' => 'معدل',
      'status' => 'الحالة',
      'is_default' => 'هو الافتراضي؟',
    ),
  ),
  'sales-taxes' => 
  array (
    'title' => 'ضرائب المبيعات',
  ),
  'email-templates' => 
  array (
    'title' => 'قوالب البريد الإلكتروني',
    'fields' => 
    array (
      'name' => 'اسم',
      'subject' => 'موضوع',
      'body' => 'الجسم',
    ),
  ),
  'companies' => 
  array (
    'title' => 'الشركات',
    'fields' => 
    array (
      'company-name' => 'اسم الشركة',
      'address' => 'عنوان',
      'business-number' => 'عدد الأعمال',
      'city' => 'مدينة',
      'url' => 'رابط',
      'state-region' => 'الدولة / المنطقة',
      'email' => 'البريد الإلكتروني',
      'zip-postal-code' => 'الرمز البريدي / الرمز البريدي',
      'country' => 'بلد',
      'phone' => 'هاتف',
      'logo' => 'شعار',
    ),
  ),
  'accounts' => 
  array (
    'title' => 'حسابات',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
      'initial-balance' => 'الرصيد الافتتاحي',
      'account-number' => 'رقم حساب',
      'contact-person' => 'الشخص الذي يمكن الاتصال به',
      'phone' => 'هاتف',
      'url' => 'URL',
    ),
  ),
  'payment-gateways' => 
  array (
    'title' => 'بوابات الدفع',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
      'logo' => 'شعار',
    ),
  ),
  'warehouses' => 
  array (
    'title' => 'المستودعات',
    'fields' => 
    array (
      'name' => 'اسم',
      'address' => 'عنوان',
      'description' => 'وصف',
    ),
  ),
  'taxes' => 
  array (
    'title' => 'الضرائب',
    'fields' => 
    array (
      'name' => 'اسم',
      'rate' => 'معدل',
      'rate-type' => 'نوع معدل',
      'description' => 'وصف',
    ),
  ),
  'discounts' => 
  array (
    'title' => 'خصومات',
    'fields' => 
    array (
      'name' => 'اسم',
      'discount' => 'خصم',
      'discount-type' => 'نوع الخصم',
      'description' => 'وصف',
    ),
  ),
  'recurring-periods' => 
  array (
    'title' => 'فترات متكررة',
    'fields' => 
    array (
      'title' => 'عنوان',
      'value' => 'القيمة',
      'description' => 'وصف',
    ),
  ),
  'languages' => 
  array (
    'title' => 'اللغات',
    'fields' => 
    array (
      'language' => 'لغة',
      'code' => 'الشفرة',
      'is-rtl' => 'هل rtl',
    ),
  ),
  'purchase-orders' => 
  array (
    'title' => 'طلبات الشراء',
    'fields' => 
    array (
      'customer' => 'المورد',
      'subject' => 'موضوع',
      'status' => 'الحالة',
      'address' => 'عنوان',
      'invoice-prefix' => 'بادئة الفاتورة',
      'show-quantity-as' => 'تظهر الكمية كما',
      'invoice-no' => 'فاتورة #',
      'reference' => 'مرجع',
      'order-date' => 'تم إصداره في',
      'order-due-date' => 'تاريخ استحقاق الطلب',
      'update-stock' => 'تحديث الأسهم',
      'notes' => 'ملاحظات',
      'currency' => 'دقة',
      'warehouse' => 'مستودع',
      'tax' => 'ضريبة',
      'discount' => 'خصم',
      'amount' => 'كمية',
    ),
  ),
  'manage-projects' => 
  array (
    'title' => 'إدارة المشاريع',
  ),
  'projects' => 
  array (
    'title' => 'مشاريع',
  ),
  'project-statuses' => 
  array (
    'title' => 'حالات المشروع',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
    ),
  ),
  'client-projects' => 
  array (
    'title' => 'مشاريع العملاء',
    'fields' => 
    array (
      'title' => 'عنوان',
      'client' => 'عميل',
      'priority' => 'أفضلية',
      'budget' => 'ميزانية',
      'billing-type' => 'نوع الفواتير',
      'phase' => 'مرحلة',
      'assigned-to' => 'مخصص ل',
      'start-date' => 'تاريخ البدء',
      'due-date' => 'تاريخ الاستحقاق',
      'status' => 'الحالة',
      'description' => 'وصف',
      'demo-url' => 'عنوان URL التجريبي',
    ),
  ),
  'project-billing-types' => 
  array (
    'title' => 'أنواع الفواتير المشروع',
    'fields' => 
    array (
      'title' => 'عنوان',
      'description' => 'وصف',
    ),
  ),
  'time-entries' => 
  array (
    'title' => 'إدخالات الوقت',
    'fields' => 
    array (
      'project' => 'مشروع',
      'start-date' => 'تاريخ البدء',
      'end-date' => 'تاريخ الانتهاء',
      'description' => 'وصف',
    ),
  ),
  'sales' => 
  array (
    'title' => 'مبيعات',
  ),
  'invoices' => 
  array (
    'title' => 'الفواتير',
    'fields' => 
    array (
      'customer' => 'زبون',
      'currency' => 'دقة',
      'title' => 'عنوان',
      'address' => 'عنوان',
      'invoice-prefix' => 'بادئة الفاتورة',
      'show-quantity-as' => 'تظهر الكمية كما',
      'invoice-no' => 'فاتورة #',
      'status' => 'الحالة',
      'reference' => 'مرجع',
      'invoice-date' => 'تاريخ الفاتورة',
      'invoice-due-date' => 'تاريخ استحقاق الفاتورة',
      'invoice-notes' => 'ملاحظات الفاتورة',
      'tax' => 'ضريبة',
      'discount' => 'خصم',
      'amount' => 'كمية',
      'discount_format' => 'تنسيق الخصم',
      'tax_format' => 'تنسيق الضريبة',
    ),
  ),
  'quotes' => 
  array (
    'title' => 'يقتبس',
    'fields' => 
    array (
      'customer' => 'زبون',
      'status' => 'الحالة',
      'title' => 'عنوان',
      'address' => 'عنوان',
      'quote-prefix' => 'بادئة اقتباس',
      'show-quantity-as' => 'تظهر الكمية كما',
      'quote-no' => 'اقتباس لا',
      'reference' => 'مرجع',
      'quote-date' => 'تاريخ الاقتباس',
      'quote-expiry-date' => 'تاريخ انتهاء الصلاحية',
      'proposal-text' => 'نص الاقتراح',
      'currency' => 'دقة',
      'tax' => 'ضريبة',
      'discount' => 'خصم',
      'amount' => 'كمية',
    ),
  ),
  'recurring-invoices' => 
  array (
    'title' => 'الفواتير المتكررة',
    'fields' => 
    array (
      'customer' => 'زبون',
      'currency' => 'دقة',
      'title' => 'عنوان',
      'address' => 'عنوان',
      'invoice-prefix' => 'بادئة الفاتورة',
      'show-quantity-as' => 'تظهر الكمية كما',
      'invoice-no' => 'فاتورة #',
      'status' => 'الحالة',
      'reference' => 'مرجع',
      'invoice-date' => 'تاريخ الفاتورة',
      'invoice-due-date' => 'تاريخ استحقاق الفاتورة',
      'invoice-notes' => 'ملاحظات الفاتورة',
      'tax' => 'ضريبة',
      'discount' => 'خصم',
      'recurring-period' => 'فترة متكررة',
      'amount' => 'كمية',
      'products' => 'منتجات',
      'paymentstatus' => 'حالة السداد',
    ),
  ),
  'contact-groups' => 
  array (
    'title' => 'مجموعات الاتصال',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
    ),
  ),
  'contact-types' => 
  array (
    'title' => 'أنواع الاتصال',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
    ),
  ),
  'contact-notes' => 
  array (
    'title' => 'ملاحظات الاتصال',
    'fields' => 
    array (
      'title' => 'عنوان',
      'contact' => 'اتصل',
      'notes' => 'ملاحظات',
      'attachment' => 'المرفق',
    ),
  ),
  'contact-documents' => 
  array (
    'title' => 'وثائق الاتصال',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
      'attachments' => 'مرفقات',
      'contact' => 'اتصل',
    ),
  ),
  'products-transfer' => 
  array (
    'title' => 'نقل المنتجات',
  ),
  'products-return' => 
  array (
    'title' => 'عودة المنتجات',
    'fields' => 
    array (
      'subject' => 'موضوع',
      'customer' => 'زبون',
      'currency' => 'دقة',
      'status' => 'الحالة',
      'address' => 'عنوان',
      'invoice-prefix' => 'بادئة الفاتورة',
      'show-quantity-as' => 'تظهر الكمية كما',
      'invoice-no' => 'فاتورة #',
      'reference' => 'مرجع',
      'order-date' => 'تاريخ الطلب',
      'order-due-date' => 'تاريخ استحقاق الطلب',
      'update-stock' => 'تحديث الأسهم',
      'notes' => 'ملاحظات',
      'tax' => 'ضريبة',
      'discount' => 'خصم',
      'ware-house' => 'وير المنزل',
    ),
  ),
  'brands' => 
  array (
    'title' => 'العلامات التجارية',
    'fields' => 
    array (
      'title' => 'عنوان',
      'icon' => 'أيقونة',
      'status' => 'الحالة',
    ),
  ),
  'database-backup' => 
  array (
    'title' => 'قاعدة بيانات النسخ الاحتياطي',
  ),
  'departments' => 
  array (
    'title' => 'الإدارات',
    'fields' => 
    array (
      'name' => 'اسم',
      'description' => 'وصف',
      'created-by' => 'صنع من قبل',
    ),
  ),
  'support' => 
  array (
    'title' => 'الدعم',
    'fields' => 
    array (
      'name' => 'اسم',
      'email' => 'البريد الإلكتروني',
      'subject' => 'موضوع',
      'department' => ' قسم، أقسام',
      'priority' => 'أفضلية',
      'description' => 'وصف',
      'attachments' => 'مرفقات',
      'created-by' => 'صنع من قبل',
      'assigned-to' => 'مخصص ل',
    ),
  ),
  'knowledgebase' => 
  array (
    'title' => 'قاعدة المعرفة',
  ),
  'transfers' => 
  array (
    'title' => 'نقل',
    'fields' => 
    array (
      'from' => 'من عند',
      'to' => 'إلى',
      'date' => 'تاريخ',
      'amount' => 'كمية',
      'ref-no' => 'الرقم المرجعي',
      'payment-method' => 'طريقة الدفع او السداد',
      'description' => 'وصف',
    ),
  ),
  'articles' => 
  array (
    'title' => 'مقالات',
    'fields' => 
    array (
      'title' => 'عنوان',
      'category-id' => 'الاقسام',
      'tag-id' => 'الكلمات',
      'page-text' => 'نص',
      'excerpt' => 'مقتطفات',
      'featured-image' => 'صورة مميزة',
      'available-for' => 'متاح لى',
    ),
  ),
  'balance-sheet' => 
  array (
    'title' => 'ورقة التوازن',
  ),
  'general-settings' => 
  array (
    'title' => 'الاعدادات العامة',
  ),
  'master-settings' => 
  array (
    'title' => 'إعدادات سيد',
    'fields' => 
    array (
      'module' => 'وحدة',
      'key' => 'مفتاح',
      'description' => 'وصف',
    ),
  ),
  'countries' => 
  array (
    'title' => 'بلدان',
    'fields' => 
    array (
      'shortcode' => 'رمز قصير',
      'title' => 'عنوان',
    ),
  ),
  'measurement-units' => 
  array (
    'title' => 'وحدات القياس',
    'fields' => 
    array (
      'title' => 'عنوان',
      'status' => 'الحالة',
      'description' => 'وصف',
    ),
  ),
  'payments' => 
  array (
    'title' => 'المدفوعات',
    'failed' => 'عملية الدفع فشلت',
    'cancelled' => 'لقد ألغيت دفعتك',
  ),
  'navigation-menues' => 
  array (
    'title' => 'قوائم التنقل',
  ),
  'app_create' => 'خلق',
  'app_save' => 'حفظ',
  'app_edit' => 'تصحيح',
  'app_restore' => 'استعادة',
  'app_values' => 'القيم',
  'app_permadel' => 'الحذف بشكل نهائي',
  'app_all' => 'الكل',
  'app_trash' => 'قمامة، يدمر، يهدم',
  'app_view' => 'رأي',
  'app_update' => 'تحديث',
  'app_list' => 'قائمة',
  'app_no_entries_in_table' => 'لا توجد إدخالات في الجدول',
  'app_custom_controller_index' => 'مؤشر تحكم مخصص.',
  'app_logout' => 'الخروج',
  'app_add_new' => 'اضف جديد',
  'app_are_you_sure' => 'هل أنت واثق؟',
  'app_back_to_list' => 'الرجوع للقائمة',
  'app_dashboard' => 'لوحة القيادة',
  'app_delete' => 'حذف',
  'app_delete_selected' => 'احذف المختار',
  'app_category' => 'الفئة',
  'app_categories' => 'الاقسام',
  'app_sample_category' => 'فئة العينة',
  'app_questions' => 'الأسئلة',
  'app_question' => 'سؤال',
  'app_answer' => 'إجابة',
  'app_sample_question' => 'نموذج السؤال',
  'app_sample_answer' => 'عينة إجابة',
  'app_faq_management' => 'إدارة التعليمات',
  'app_administrator_can_create_other_users' => 'المسؤول (يمكنه إنشاء مستخدمين آخرين)',
  'app_simple_user' => 'مستخدم بسيط',
  'app_title' => 'عنوان',
  'app_roles' => 'الأدوار',
  'app_role' => 'وظيفة',
  'app_user_management' => 'إدارةالمستخدم',
  'app_users' => 'المستخدمين',
  'app_user' => 'المستعمل',
  'app_name' => 'اسم',
  'app_email' => 'البريد الإلكتروني',
  'app_password' => 'كلمه السر',
  'app_remember_token' => 'تذكر الرمز',
  'app_permissions' => 'أذونات',
  'app_user_actions' => 'إجراءات المستخدم',
  'app_action' => 'عمل',
  'app_action_model' => 'نموذج العمل',
  'app_action_id' => 'معرف العمل',
  'app_time' => 'زمن',
  'app_campaign' => 'حملة',
  'app_campaigns' => 'حملات',
  'app_description' => 'وصف',
  'app_valid_from' => 'صالح من تاريخ',
  'app_valid_to' => 'صالحة ل',
  'app_discount_amount' => 'مقدار الخصم',
  'app_discount_percent' => 'خصم في المئة',
  'app_coupons_amount' => 'كوبونات المبلغ',
  'app_coupons' => 'كوبونات',
  'app_code' => 'الشفرة',
  'app_redeem_time' => 'استرداد الوقت',
  'app_coupon_management' => 'إدارة القسيمة',
  'app_time_management' => 'إدارة الوقت',
  'app_projects' => 'مشاريع',
  'app_reports' => 'تقارير',
  'app_time_entries' => 'إدخالات الوقت',
  'app_work_type' => 'نوع العمل',
  'app_work_types' => 'أنواع العمل',
  'app_project' => 'مشروع',
  'app_start_time' => 'وقت البدء',
  'app_end_time' => 'وقت النهاية',
  'app_expense_category' => 'فئة النفقات',
  'app_expense_categories' => 'فئات النفقات',
  'app_expense_management' => 'إدارة حساب',
  'app_expenses' => 'نفقات',
  'app_expense' => 'مصروف',
  'app_entry_date' => 'موعد الدخول',
  'app_amount' => 'كمية',
  'app_income_categories' => 'فئات الدخل',
  'app_monthly_report' => 'التقرير الشهري',
  'app_companies' => 'الشركات',
  'app_company_name' => 'اسم الشركة',
  'app_address' => 'عنوان',
  'app_website' => 'موقع الكتروني',
  'app_contact_management' => 'إدارة الاتصال',
  'app_contacts' => 'جهات الاتصال',
  'app_company' => 'شركة',
  'app_first_name' => 'الاسم الاول',
  'app_last_name' => 'الكنية',
  'app_phone' => 'هاتف',
  'app_phone1' => 'الهاتف 1',
  'app_phone2' => 'الهاتف 2',
  'app_skype' => 'سكايب',
  'app_photo' => 'الصورة (بحد أقصى 8 ميجابايت)',
  'app_category_name' => 'اسم التصنيف',
  'app_product_management' => 'ادارة المنتج',
  'app_products' => 'منتجات',
  'app_product_name' => 'اسم المنتج',
  'app_price' => 'السعر',
  'app_tags' => 'الكلمات',
  'app_tag' => 'بطاقة',
  'app_photo1' => 'Photo1',
  'app_photo2' => 'Photo2',
  'app_photo3' => 'photo3 من',
  'app_calendar' => 'التقويم',
  'app_statuses' => 'الأوضاع',
  'app_task_management' => 'ادارة المهام',
  'app_tasks' => 'مهام',
  'app_status' => 'الحالة',
  'app_attachment' => 'المرفق',
  'app_due_date' => 'تاريخ الاستحقاق',
  'app_assigned_to' => 'مخصص ل',
  'app_assets' => 'الأصول',
  'app_asset' => 'الأصول',
  'app_serial_number' => 'رقم سري',
  'app_location' => 'موقعك',
  'app_locations' => 'مواقع',
  'app_assigned_user' => 'معين (مستخدم)',
  'app_notes' => 'ملاحظات',
  'app_assets_history' => 'تاريخ الأصول',
  'app_assets_management' => 'إدارة الأصول',
  'app_slug' => 'سبيكة',
  'app_content_management' => 'ادارة المحتوى',
  'app_text' => 'نص',
  'app_excerpt' => 'مقتطفات',
  'app_featured_image' => 'صورة مميزة',
  'app_pages' => 'صفحات',
  'app_axis' => 'محور',
  'app_show' => 'تبين',
  'app_group_by' => 'مجموعة من',
  'app_chart_type' => 'نوع الرسم البياني',
  'app_create_new_report' => 'إنشاء تقرير جديد',
  'app_no_reports_yet' => 'لا توجد تقارير حتى الآن.',
  'app_created_at' => 'أنشئت في',
  'app_updated_at' => 'تم التحديث في',
  'app_deleted_at' => 'تم الحذف في',
  'app_reports_x_axis_field' => 'المحور السيني - يرجى اختيار أحد حقول التاريخ / الوقت',
  'app_reports_y_axis_field' => 'المحور ص - الرجاء اختيار أحد حقول الأرقام',
  'app_select_crud_placeholder' => 'يرجى اختيار واحدة من CRUDs الخاص بك',
  'app_select_dt_placeholder' => 'يرجى اختيار واحد من حقول التاريخ / الوقت',
  'app_aggregate_function_use' => 'مجموع وظيفة للاستخدام',
  'app_x_axis_group_by' => 'مجموعة المحور س',
  'app_x_axis_field' => 'مجال المحور السيني (التاريخ / الوقت)',
  'app_y_axis_field' => 'مجال المحور ص',
  'app_integer_float_placeholder' => 'يرجى اختيار أحد الحقول الصحيحة / الطافية',
  'app_change_notifications_field_1_label' => 'إرسال إشعار البريد الإلكتروني للمستخدم',
  'app_change_notifications_field_2_label' => 'عند الدخول على CRUD',
  'app_select_users_placeholder' => 'يرجى اختيار واحد من المستخدمين لديك',
  'app_is_created' => 'تم إنشاؤه',
  'app_is_updated' => 'يتم تحديث',
  'app_is_deleted' => 'يتم حذف',
  'app_notifications' => 'إخطارات',
  'app_notify_user' => 'أبلغ المستخدم',
  'app_when_crud' => 'عندما الخام',
  'app_create_new_notification' => 'إنشاء إشعار جديد',
  'app_stripe_transactions' => 'المعاملات الشريط',
  'app_upgrade_to_premium' => 'الترقية إلى بريميوم',
  'app_messages' => 'رسائل',
  'app_you_have_no_messages' => 'ليس لديك رسائل.',
  'app_all_messages' => 'جميع الرسائل',
  'app_new_message' => 'رسالة جديدة',
  'app_outbox' => 'صندوق الحفظ',
  'app_inbox' => 'صندوق الوارد',
  'app_recipient' => 'مستلم',
  'app_subject' => 'موضوع',
  'app_message' => 'رسالة',
  'app_send' => 'إرسال',
  'app_reply' => 'الرد',
  'app_calendar_sources' => 'مصادر التقويم',
  'app_new_calendar_source' => 'إنشاء مصدر تقويم جديد',
  'app_crud_title' => 'العنوان الخام',
  'app_crud_date_field' => 'حقل تاريخ الخام',
  'app_prefix' => 'اختصار',
  'app_label_field' => 'حقل التسمية',
  'app_suffix' => 'Sufix',
  'app_no_calendar_sources' => 'لا توجد مصادر للتقويم حتى الآن.',
  'app_crud_event_field' => 'مجال تسمية الحدث',
  'app_create_new_calendar_source' => 'إنشاء مصدر تقويم جديد',
  'app_edit_calendar_source' => 'تحرير مصدر التقويم',
  'app_client_management' => 'إدارة العملاء',
  'app_client_management_settings' => 'إعدادات إدارة العميل',
  'app_country' => 'بلد',
  'app_client_status' => 'حالة العميل',
  'app_clients' => 'عملاء',
  'app_client_statuses' => 'حالات العميل',
  'app_currencies' => 'العملات',
  'app_main_currency' => 'العملة الرئيسية',
  'app_documents' => 'مستندات',
  'app_file' => 'ملف',
  'app_income_source' => 'مصدر دخل',
  'app_income_sources' => 'مصادر الدخل',
  'app_fee_percent' => 'رسوم في المئة',
  'app_note_text' => 'ملاحظة النص',
  'app_client' => 'عميل',
  'app_start_date' => 'تاريخ البدء',
  'app_budget' => 'ميزانية',
  'app_project_status' => 'حالة المشروع',
  'app_project_statuses' => 'حالات المشروع',
  'app_transactions' => 'المعاملات',
  'app_transaction_types' => 'أنواع المعاملات',
  'app_transaction_type' => 'نوع المعاملة',
  'app_transaction_date' => 'تاريخ الصفقة',
  'app_currency' => 'دقة',
  'app_current_password' => 'كلمة المرور الحالي',
  'app_new_password' => 'كلمة السر الجديدة',
  'app_password_confirm' => 'تأكيد كلمة السر الجديدة',
  'app_dashboard_text' => 'لقد سجلت الدخول!',
  'app_forgot_password' => 'نسيت رقمك السري؟',
  'app_remember_me' => 'تذكرنى',
  'app_login' => 'تسجيل الدخول',
  'app_change_password' => 'غير كلمة السر',
  'app_csv' => 'CSV',
  'app_print' => 'طباعة',
  'app_excel' => 'تفوق',
  'app_copy' => 'نسخ',
  'app_colvis' => 'رؤية العمود',
  'app_pdf' => 'PDF',
  'app_reset_password' => 'إعادة تعيين كلمة المرور',
  'app_reset_password_woops' => '<strong>يصيح!</strong> كانت هناك مشاكل في الإدخال:',
  'app_email_line1' => 'أنت تتلقى هذا البريد الإلكتروني لأننا تلقينا طلب إعادة تعيين كلمة المرور لحسابك.',
  'app_email_line2' => 'إذا لم تطلب إعادة تعيين كلمة المرور ، فلا يلزم اتخاذ أي إجراء إضافي',
  'app_email_greet' => 'مرحبا',
  'app_email_regards' => 'مع تحياتي',
  'app_confirm_password' => 'تأكيد كلمة المرور',
  'app_if_you_are_having_trouble' => 'إذا كنت تواجه مشكلة في النقر فوق',
  'app_copy_paste_url_bellow' => 'زر ، انسخ والصق عنوان URL أدناه في متصفح الويب الخاص بك:',
  'app_please_select' => 'يرجى اختيار',
  'app_register' => 'تسجيل',
  'app_registration' => 'التسجيل',
  'app_not_approved_title' => 'أنت غير معتمد',
  'app_not_approved_p' => 'حسابك لا يزال غير معتمد من قبل المسؤول. يرجى التحلي بالصبر والمحاولة مرة أخرى في وقت لاحق.',
  'app_there_were_problems_with_input' => 'كانت هناك مشاكل مع المدخلات',
  'app_whoops' => 'يصيح!',
  'app_file_contains_header_row' => 'يحتوي الملف على صف الرأس؟',
  'app_csvImport' => 'استيراد ملف CSV',
  'app_csv_file_to_import' => 'ملف CSV للاستيراد',
  'app_parse_csv' => 'تحليل CSV',
  'app_import_data' => 'بيانات الاستيراد',
  'app_imported_rows_to_table' => 'المستوردة: الصفوف الصفوف إلى: الجدول الجدول',
  'app_subscription-billing' => 'الاشتراكات',
  'app_subscription-payments' => 'المدفوعات',
  'app_basic_crm' => 'إدارة علاقات العملاء الأساسية',
  'app_customers' => 'الزبائن',
  'app_customer' => 'زبون',
  'app_select_all' => 'اختر الكل',
  'app_deselect_all' => 'الغاء تحديد الكل',
  'app_team-management' => 'فرق',
  'app_team-management-singular' => 'الفريق',
  'global_title' => 'ديجي المحاسبة ، الفواتير وفواتير CRM',
  'app_add_key' => 'إضافة مفتاح',
  'app_settings' => 'الإعدادات',
  'app_make_default' => 'جعل الافتراضي',
  'info' => 'معلومات',
  'operations_disabled' => 'تم تعطيل العملية في الوضع التجريبي',
  'download-template' => 'تحميل القالب',
  'app_refresh' => 'تحديث',
  'app_loading' => 'جار التحميل',
);
