<?php

return array (
  'user-management' => 
  array (
    'title' => 'उपयोगकर्ता प्रबंधन',
  ),
  'permissions' => 
  array (
    'title' => 'अनुमतियां',
    'fields' => 
    array (
      'title' => 'शीर्षक',
    ),
  ),
  'roles' => 
  array (
    'title' => 'भूमिकाएँ',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'permission' => 'अनुमतियां',
    ),
  ),
  'users' => 
  array (
    'title' => 'उपयोगकर्ता',
    'active' => 'सक्रिय',
    'suspend' => 'निलंबित करें',
    'activate' => 'सक्रिय',
    'fields' => 
    array (
      'name' => 'नाम',
      'email' => 'ईमेल',
      'password' => 'पारण शब्द',
      'role' => 'भूमिका',
      'remember-token' => 'टोकन याद रखें',
      'contact-reference' => 'संदर्भ से संपर्क करें',
      'department' => 'विभाग',
      'status' => 'स्थिति',
    ),
  ),
  'user-actions' => 
  array (
    'title' => 'उपयोगकर्ता क्रियाएँ',
    'created_at' => 'पहर',
    'fields' => 
    array (
      'user' => 'उपयोगकर्ता',
      'action' => 'कार्य',
      'action-model' => 'एक्शन मॉडल',
      'action-id' => 'कार्रवाई आईडी',
    ),
  ),
  'contact-management' => 
  array (
    'title' => 'संपर्क प्रबंधन',
  ),
  'contact-companies' => 
  array (
    'title' => 'कंपनियों',
    'fields' => 
    array (
      'name' => 'कंपनी का नाम',
      'email' => 'ईमेल',
      'address' => 'पता',
      'website' => 'वेबसाइट',
    ),
  ),
  'contacts' => 
  array (
    'title' => 'संपर्क',
    'title_customers' => 'ग्राहकों',
    'title_customer' => 'ग्राहक',
    'title_suppliers' => 'आपूर्तिकर्ता',
    'title_supplier' => 'प्रदायक',
    'title_leads' => 'सुराग',
    'title_lead' => 'लीड',
    'title_profile' => 'प्रोफाइल',
    'edit-profile' => 'प्रोफाइल एडिट करें',
    'fields' => 
    array (
      'company' => 'कंपनी',
      'group' => 'समूह',
      'contact-type' => 'संपर्क प्रकार',
      'first-name' => 'पहला नाम',
      'last-name' => 'अंतिम नाम',
      'language' => 'भाषा',
      'phone1' => 'फोन 1',
      'phone2' => 'फ़ोन २',
      'phone1_code' => 'फोन 1 कोड',
      'phone2_code' => 'फोन 2 कोड',
      'email' => 'ईमेल',
      'skype' => 'स्काइप',
      'address' => 'पता',
      'city' => 'शहर',
      'state-region' => 'राज्य / क्षेत्र',
      'zip-postal-code' => 'ज़िप / पोस्टल कोड',
      'tax-id' => 'टैक्स आईडी',
      'country' => 'देश',
    ),
  ),
  'expense-management' => 
  array (
    'title' => 'लेखांकन',
  ),
  'expense-category' => 
  array (
    'title' => 'व्यय श्रेणियाँ',
    'fields' => 
    array (
      'name' => 'नाम',
    ),
  ),
  'income-category' => 
  array (
    'title' => 'आय श्रेणियों',
    'fields' => 
    array (
      'name' => 'नाम',
    ),
  ),
  'income' => 
  array (
    'title' => 'आय',
    'fields' => 
    array (
      'account' => 'लेखा',
      'income-category' => 'आय श्रेणी',
      'entry-date' => 'प्रवेश की तिथि',
      'amount' => 'रकम',
      'description' => 'विवरण',
      'description-file' => 'विवरण फ़ाइल',
      'payer' => 'भुगतानकर्ता',
      'pay-method' => 'वेतन विधि',
      'ref-no' => 'रेफरी #',
    ),
  ),
  'expense' => 
  array (
    'title' => 'व्यय',
    'fields' => 
    array (
      'account' => 'लेखा',
      'expense-category' => 'व्यय की श्रेणी',
      'entry-date' => 'प्रवेश की तिथि',
      'amount' => 'रकम',
      'description' => 'विवरण',
      'description-file' => 'विवरण फ़ाइल',
      'payee' => 'आदाता',
      'payment-method' => 'भुगतान का तरीका',
      'ref-no' => 'रेफरी #',
    ),
  ),
  'monthly-report' => 
  array (
    'title' => 'मासिक रिपोर्ट',
  ),
  'faq-management' => 
  array (
    'title' => 'प्रबंधन',
    'faq' => 'पूछे जाने वाले प्रश्न',
  ),
  'faq-categories' => 
  array (
    'title' => 'श्रेणियाँ',
    'fields' => 
    array (
      'title' => 'वर्ग',
    ),
  ),
  'faq-questions' => 
  array (
    'title' => 'प्रशन',
    'fields' => 
    array (
      'category' => 'वर्ग',
      'question-text' => 'सवाल',
      'answer-text' => 'उत्तर',
    ),
  ),
  'internal-notifications' => 
  array (
    'title' => 'सूचनाएं',
    'fields' => 
    array (
      'text' => 'टेक्स्ट',
      'link' => 'संपर्क',
      'users' => 'उपयोगकर्ता',
    ),
  ),
  'task-management' => 
  array (
    'title' => 'कार्य प्रबंधन',
  ),
  'task-statuses' => 
  array (
    'title' => 'वे स्थितियां',
    'panel-default' => 'डिफ़ॉल्ट पैनल',
    'panel-primary' => 'प्राथमिक पैनल',
    'panel-success' => 'सफलता पैनल',
    'panel-info' => 'जानकारी पैनल',
    'panel-warning' => 'चेतावनी पैनल',
    'panel-danger' => 'खतरे का पैनल',
    'fields' => 
    array (
      'name' => 'नाम',
      'color' => 'रंग',
    ),
  ),
  'task-tags' => 
  array (
    'title' => 'टैग',
    'fields' => 
    array (
      'name' => 'नाम',
    ),
  ),
  'tasks' => 
  array (
    'title' => 'कार्य',
    'task-status-updated' => 'टास्क की स्थिति सफलतापूर्वक अपडेट की गई',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
      'status' => 'स्थिति',
      'tag' => 'टैग',
      'attachment' => 'आसक्ति',
      'start-date' => 'आरंभ करने की तिथि',
      'due-date' => 'नियत तारीख',
      'user' => 'को सौंपना',
    ),
  ),
  'task-calendar' => 
  array (
    'title' => 'कैलेंडर',
    'status-wise' => 'स्थिति वार',
  ),
  'content-management' => 
  array (
    'title' => 'सामग्री प्रबंधन',
  ),
  'content-categories' => 
  array (
    'title' => 'श्रेणियाँ',
    'fields' => 
    array (
      'title' => 'वर्ग',
      'slug' => 'काउंटर',
    ),
  ),
  'content-tags' => 
  array (
    'title' => 'टैग',
    'fields' => 
    array (
      'title' => 'टैग',
      'slug' => 'काउंटर',
    ),
  ),
  'content-pages' => 
  array (
    'title' => 'पेज',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'category-id' => 'श्रेणियाँ',
      'tag-id' => 'टैग',
      'page-text' => 'टेक्स्ट',
      'excerpt' => 'अंश',
      'featured-image' => 'निरूपित चित्र',
      'created-at' => 'समय बनाया गया',
    ),
  ),
  'product-management' => 
  array (
    'title' => 'उत्पाद प्रबंधन',
  ),
  'product-categories' => 
  array (
    'title' => 'श्रेणियाँ',
    'fields' => 
    array (
      'name' => 'श्रेणी नाम',
      'description' => 'विवरण',
      'photo' => 'फोटो (अधिकतम 8mb)',
    ),
  ),
  'product-tags' => 
  array (
    'title' => 'टैग',
    'fields' => 
    array (
      'name' => 'नाम',
    ),
  ),
  'products' => 
  array (
    'title' => 'उत्पाद',
    'gallery-file-types' => 'स्वीकृत फ़ाइल प्रकार: png, jpg, jpeg, gif',
    'fields' => 
    array (
      'name' => 'उत्पाद का नाम',
      'product-code' => 'उत्पाद कोड',
      'actual-price' => 'वास्तविक कीमत',
      'sale-price' => 'विक्रय कीमत',
      'category' => 'वर्ग',
      'tag' => 'टैग',
      'ware-house' => 'वेयर हाउस',
      'description' => 'विवरण',
      'excerpt' => 'अंश',
      'stock-quantity' => 'शेयर मात्रा',
      'alert-quantity' => 'चेतावनी मात्रा',
      'image-gallery' => 'छवि गैलरी',
      'thumbnail' => 'थंबनेल',
      'other-files' => 'अन्य फ़ाइलें',
      'hsn-sac-code' => 'HSN / SAC कोड',
      'product-size' => 'उत्पाद का आकार',
      'product-weight' => 'उत्पाद - भार',
      'brand' => 'ब्रांड',
    ),
  ),
  'assets-management' => 
  array (
    'title' => 'संपत्ति प्रबंधन',
  ),
  'assets-categories' => 
  array (
    'title' => 'श्रेणियाँ',
    'fields' => 
    array (
      'title' => 'शीर्षक',
    ),
  ),
  'assets-statuses' => 
  array (
    'title' => 'वे स्थितियां',
    'fields' => 
    array (
      'title' => 'शीर्षक',
    ),
  ),
  'assets-locations' => 
  array (
    'title' => 'स्थान',
    'fields' => 
    array (
      'title' => 'शीर्षक',
    ),
  ),
  'assets' => 
  array (
    'title' => 'संपत्ति',
    'fields' => 
    array (
      'category' => 'वर्ग',
      'serial-number' => 'क्रमांक',
      'title' => 'शीर्षक',
      'photo1' => 'थंबनेल',
      'photo2' => 'गेलरी',
      'attachments' => 'संलग्नक',
      'status' => 'स्थिति',
      'location' => 'स्थान',
      'assigned-user' => 'सौंपा (उपयोगकर्ता)',
      'notes' => 'टिप्पणियाँ',
    ),
  ),
  'assets-history' => 
  array (
    'title' => 'इतिहास मान लेता है',
    'created_at' => 'पहर',
    'fields' => 
    array (
      'asset' => 'एसेट',
      'status' => 'स्थिति',
      'location' => 'स्थान',
      'assigned-user' => 'सौंपा (उपयोगकर्ता)',
    ),
  ),
  'coupon-management' => 
  array (
    'title' => 'कूपन प्रबंधन',
  ),
  'coupon-campaigns' => 
  array (
    'title' => 'अभियान',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'description' => 'विवरण',
      'valid-from' => 'से मान्य',
      'valid-to' => 'इस तक मान्य',
      'discount-amount' => 'छूट राशि',
      'discount-percent' => 'छूट का प्रतिशत',
      'coupons-amount' => 'कूपन राशि',
    ),
  ),
  'coupons' => 
  array (
    'title' => 'कूपन',
    'fields' => 
    array (
      'campaign' => 'अभियान',
      'code' => 'कोड',
      'valid-from' => 'से मान्य',
      'valid-to' => 'इस तक मान्य',
      'discount-amount' => 'छूट राशि',
      'discount-percent' => 'छूट का प्रतिशत',
      'redeem-time' => 'रिडीम समय',
    ),
  ),
  'expense-types' => 
  array (
    'title' => 'व्यय प्रकार',
  ),
  'global-settings' => 
  array (
    'title' => 'वैश्विक व्यवस्था',
  ),
  'currencies' => 
  array (
    'title' => 'मुद्राओं',
    'fields' => 
    array (
      'name' => 'नाम',
      'symbol' => 'प्रतीक',
      'code' => 'कोड',
      'rate' => 'मूल्यांकन करें',
      'status' => 'स्थिति',
      'is_default' => 'डिफ़ॉल्ट है?',
    ),
  ),
  'sales-taxes' => 
  array (
    'title' => 'बिक्री कर',
  ),
  'email-templates' => 
  array (
    'title' => 'ईमेल टेम्प्लेट',
    'fields' => 
    array (
      'name' => 'नाम',
      'subject' => 'विषय',
      'body' => 'तन',
    ),
  ),
  'companies' => 
  array (
    'title' => 'कंपनियों',
    'fields' => 
    array (
      'company-name' => 'कंपनी का नाम',
      'address' => 'पता',
      'business-number' => 'व्यापार संबंधी अंक',
      'city' => 'शहर',
      'url' => 'यूआरएल',
      'state-region' => 'राज्य / क्षेत्र',
      'email' => 'ईमेल',
      'zip-postal-code' => 'ज़िप / पोस्टल कोड',
      'country' => 'देश',
      'phone' => 'फ़ोन',
      'logo' => 'प्रतीक चिन्ह',
    ),
  ),
  'accounts' => 
  array (
    'title' => 'हिसाब किताब',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
      'initial-balance' => 'प्रारंभिक संतुलन',
      'account-number' => 'खाता संख्या',
      'contact-person' => 'सम्पर्क सूत्र',
      'phone' => 'फ़ोन',
      'url' => 'यूआरएल',
    ),
  ),
  'payment-gateways' => 
  array (
    'title' => 'भुगतान द्वार',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
      'logo' => 'प्रतीक चिन्ह',
    ),
  ),
  'warehouses' => 
  array (
    'title' => 'गोदामों',
    'fields' => 
    array (
      'name' => 'नाम',
      'address' => 'पता',
      'description' => 'विवरण',
    ),
  ),
  'taxes' => 
  array (
    'title' => 'करों',
    'fields' => 
    array (
      'name' => 'नाम',
      'rate' => 'मूल्यांकन करें',
      'rate-type' => 'दर प्रकार',
      'description' => 'विवरण',
    ),
  ),
  'discounts' => 
  array (
    'title' => 'छूट',
    'fields' => 
    array (
      'name' => 'नाम',
      'discount' => 'छूट',
      'discount-type' => 'डिस्काउंट प्रकार',
      'description' => 'विवरण',
    ),
  ),
  'recurring-periods' => 
  array (
    'title' => 'आवर्ती अवधि',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'value' => 'मूल्य',
      'description' => 'विवरण',
    ),
  ),
  'languages' => 
  array (
    'title' => 'बोली',
    'fields' => 
    array (
      'language' => 'भाषा',
      'code' => 'कोड',
      'is-rtl' => 'Rtl है',
    ),
  ),
  'purchase-orders' => 
  array (
    'title' => 'क्रय आदेश',
    'fields' => 
    array (
      'customer' => 'प्रदायक',
      'subject' => 'विषय',
      'status' => 'स्थिति',
      'address' => 'पता',
      'invoice-prefix' => 'चालान उपसर्ग',
      'show-quantity-as' => 'मात्रा के रूप में दिखाएं',
      'invoice-no' => 'चालान #',
      'reference' => 'संदर्भ',
      'order-date' => 'पर जारी किया',
      'order-due-date' => 'नियत तिथि पर आदेश',
      'update-stock' => 'स्टॉक अपडेट करें',
      'notes' => 'टिप्पणियाँ',
      'currency' => 'मुद्रा',
      'warehouse' => 'गोदाम',
      'tax' => 'कर',
      'discount' => 'छूट',
      'amount' => 'रकम',
    ),
  ),
  'manage-projects' => 
  array (
    'title' => 'परियोजनाओं का प्रबंधन करें',
  ),
  'projects' => 
  array (
    'title' => 'परियोजनाओं',
  ),
  'project-statuses' => 
  array (
    'title' => 'परियोजना की स्थिति',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
    ),
  ),
  'client-projects' => 
  array (
    'title' => 'ग्राहक परियोजनाओं',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'client' => 'ग्राहक',
      'priority' => 'प्राथमिकता',
      'budget' => 'बजट',
      'billing-type' => 'बिलिंग प्रकार',
      'phase' => 'अवस्था',
      'assigned-to' => 'को सौंपना',
      'start-date' => 'आरंभ करने की तिथि',
      'due-date' => 'नियत तारीख',
      'status' => 'स्थिति',
      'description' => 'विवरण',
      'demo-url' => 'डेमो यूआरएल',
    ),
  ),
  'project-billing-types' => 
  array (
    'title' => 'प्रोजेक्ट बिलिंग प्रकार',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'description' => 'विवरण',
    ),
  ),
  'time-entries' => 
  array (
    'title' => 'समय की प्रविष्टियाँ',
    'fields' => 
    array (
      'project' => 'परियोजना',
      'start-date' => 'आरंभ करने की तिथि',
      'end-date' => 'अंतिम तिथि',
      'description' => 'विवरण',
    ),
  ),
  'sales' => 
  array (
    'title' => 'बिक्री',
  ),
  'invoices' => 
  array (
    'title' => 'चालान',
    'fields' => 
    array (
      'customer' => 'ग्राहक',
      'currency' => 'मुद्रा',
      'title' => 'शीर्षक',
      'address' => 'पता',
      'invoice-prefix' => 'चालान उपसर्ग',
      'show-quantity-as' => 'मात्रा के रूप में दिखाएं',
      'invoice-no' => 'चालान #',
      'status' => 'स्थिति',
      'reference' => 'संदर्भ',
      'invoice-date' => 'चालान की तारीख',
      'invoice-due-date' => 'बिल की देय तिथि',
      'invoice-notes' => 'चालान नोट',
      'tax' => 'कर',
      'discount' => 'छूट',
      'amount' => 'रकम',
      'discount_format' => 'छूट प्रारूप',
      'tax_format' => 'कर प्रारूप',
    ),
  ),
  'quotes' => 
  array (
    'title' => 'उल्लेख। उद्धरण',
    'fields' => 
    array (
      'customer' => 'ग्राहक',
      'status' => 'स्थिति',
      'title' => 'शीर्षक',
      'address' => 'पता',
      'quote-prefix' => 'भाव उपसर्ग',
      'show-quantity-as' => 'मात्रा के रूप में दिखाएं',
      'quote-no' => 'बोली नहीं',
      'reference' => 'संदर्भ',
      'quote-date' => 'भाव तिथि',
      'quote-expiry-date' => 'एक्सपायरी डेट का उद्धरण दें',
      'proposal-text' => 'प्रस्ताव पाठ',
      'currency' => 'मुद्रा',
      'tax' => 'कर',
      'discount' => 'छूट',
      'amount' => 'रकम',
    ),
  ),
  'recurring-invoices' => 
  array (
    'title' => 'आवर्ती चालान',
    'fields' => 
    array (
      'customer' => 'ग्राहक',
      'currency' => 'मुद्रा',
      'title' => 'शीर्षक',
      'address' => 'पता',
      'invoice-prefix' => 'चालान उपसर्ग',
      'show-quantity-as' => 'मात्रा के रूप में दिखाएं',
      'invoice-no' => 'चालान #',
      'status' => 'स्थिति',
      'reference' => 'संदर्भ',
      'invoice-date' => 'चालान की तारीख',
      'invoice-due-date' => 'बिल की देय तिथि',
      'invoice-notes' => 'चालान नोट',
      'tax' => 'कर',
      'discount' => 'छूट',
      'recurring-period' => 'आवर्ती अवधि',
      'amount' => 'रकम',
      'products' => 'उत्पाद',
      'paymentstatus' => 'भुगतान की स्थिति',
    ),
  ),
  'contact-groups' => 
  array (
    'title' => 'समूहों से संपर्क करें',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
    ),
  ),
  'contact-types' => 
  array (
    'title' => 'संपर्क प्रकार',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
    ),
  ),
  'contact-notes' => 
  array (
    'title' => 'संपर्क नोट',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'contact' => 'संपर्क करें',
      'notes' => 'टिप्पणियाँ',
      'attachment' => 'आसक्ति',
    ),
  ),
  'contact-documents' => 
  array (
    'title' => 'दस्तावेजों से संपर्क करें',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
      'attachments' => 'संलग्नक',
      'contact' => 'संपर्क करें',
    ),
  ),
  'products-transfer' => 
  array (
    'title' => 'उत्पाद स्थानांतरण',
  ),
  'products-return' => 
  array (
    'title' => 'उत्पाद लौटते हैं',
    'fields' => 
    array (
      'subject' => 'विषय',
      'customer' => 'ग्राहक',
      'currency' => 'मुद्रा',
      'status' => 'स्थिति',
      'address' => 'पता',
      'invoice-prefix' => 'चालान उपसर्ग',
      'show-quantity-as' => 'मात्रा के रूप में दिखाएं',
      'invoice-no' => 'चालान #',
      'reference' => 'संदर्भ',
      'order-date' => 'आदेश की तारीख',
      'order-due-date' => 'नियत तिथि पर आदेश',
      'update-stock' => 'स्टॉक अपडेट करें',
      'notes' => 'टिप्पणियाँ',
      'tax' => 'कर',
      'discount' => 'छूट',
      'ware-house' => 'वेयर हाउस',
    ),
  ),
  'brands' => 
  array (
    'title' => 'ब्रांड्स',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'icon' => 'चिह्न',
      'status' => 'स्थिति',
    ),
  ),
  'database-backup' => 
  array (
    'title' => 'डेटाबेस बैकअप',
  ),
  'departments' => 
  array (
    'title' => 'विभागों',
    'fields' => 
    array (
      'name' => 'नाम',
      'description' => 'विवरण',
      'created-by' => 'के द्वारा बनाई गई',
    ),
  ),
  'support' => 
  array (
    'title' => 'समर्थन',
    'fields' => 
    array (
      'name' => 'नाम',
      'email' => 'ईमेल',
      'subject' => 'विषय',
      'department' => 'विभाग',
      'priority' => 'प्राथमिकता',
      'description' => 'विवरण',
      'attachments' => 'संलग्नक',
      'created-by' => 'के द्वारा बनाई गई',
      'assigned-to' => 'को सौंपना',
    ),
  ),
  'knowledgebase' => 
  array (
    'title' => 'ज्ञानधार',
  ),
  'transfers' => 
  array (
    'title' => 'स्थानांतरण',
    'fields' => 
    array (
      'from' => 'से',
      'to' => 'सेवा मेरे',
      'date' => 'दिनांक',
      'amount' => 'रकम',
      'ref-no' => 'रेफरी #',
      'payment-method' => 'भुगतान का तरीका',
      'description' => 'विवरण',
    ),
  ),
  'articles' => 
  array (
    'title' => 'सामग्री',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'category-id' => 'श्रेणियाँ',
      'tag-id' => 'टैग',
      'page-text' => 'टेक्स्ट',
      'excerpt' => 'अंश',
      'featured-image' => 'निरूपित चित्र',
      'available-for' => 'के लिए उपलब्ध है',
    ),
  ),
  'balance-sheet' => 
  array (
    'title' => 'तुलन पत्र',
  ),
  'general-settings' => 
  array (
    'title' => 'सामान्य सेटिंग्स',
  ),
  'master-settings' => 
  array (
    'title' => 'मास्टर सेटिंग्स',
    'fields' => 
    array (
      'module' => 'मॉड्यूल',
      'key' => 'कुंजी',
      'description' => 'विवरण',
    ),
  ),
  'countries' => 
  array (
    'title' => 'देश',
    'fields' => 
    array (
      'shortcode' => 'छोटे संकेत',
      'title' => 'शीर्षक',
    ),
  ),
  'measurement-units' => 
  array (
    'title' => 'नाप की इकाइयां',
    'fields' => 
    array (
      'title' => 'शीर्षक',
      'status' => 'स्थिति',
      'description' => 'विवरण',
    ),
  ),
  'payments' => 
  array (
    'title' => 'भुगतान',
    'failed' => 'भुगतान असफल हुआ',
    'cancelled' => 'आपने अपना भुगतान रद्द कर दिया है',
  ),
  'navigation-menues' => 
  array (
    'title' => 'नेविगेशन मेन्स',
  ),
  'app_create' => 'सर्जन करना',
  'app_save' => 'बचाना',
  'app_edit' => 'संपादित करें',
  'app_restore' => 'पुनर्स्थापित',
  'app_values' => 'मान',
  'app_permadel' => 'स्थायी रूप से मिटाएं',
  'app_all' => 'सब',
  'app_trash' => 'कचरा',
  'app_view' => 'राय',
  'app_update' => 'अद्यतन करें',
  'app_list' => 'सूची',
  'app_no_entries_in_table' => 'तालिका में कोई प्रविष्टि नहीं',
  'app_custom_controller_index' => 'कस्टम नियंत्रक सूचकांक।',
  'app_logout' => 'लोग आउट',
  'app_add_new' => 'नया जोड़ें',
  'app_are_you_sure' => 'क्या आपको यकीन है?',
  'app_back_to_list' => 'दोबारा सूची को जाएं',
  'app_dashboard' => 'डैशबोर्ड',
  'app_delete' => 'हटाना',
  'app_delete_selected' => 'चयनित मिटाएं',
  'app_category' => 'वर्ग',
  'app_categories' => 'श्रेणियाँ',
  'app_sample_category' => 'नमूना श्रेणी',
  'app_questions' => 'प्रशन',
  'app_question' => 'सवाल',
  'app_answer' => 'उत्तर',
  'app_sample_question' => 'नमूना सवाल',
  'app_sample_answer' => 'नमूना जवाब',
  'app_faq_management' => 'प्रबंधन',
  'app_administrator_can_create_other_users' => 'व्यवस्थापक (अन्य उपयोगकर्ता बना सकते हैं)',
  'app_simple_user' => 'सरल उपयोगकर्ता',
  'app_title' => 'शीर्षक',
  'app_roles' => 'भूमिकाएँ',
  'app_role' => 'भूमिका',
  'app_user_management' => 'उपयोगकर्ता प्रबंधन',
  'app_users' => 'उपयोगकर्ता',
  'app_user' => 'उपयोगकर्ता',
  'app_name' => 'नाम',
  'app_email' => 'ईमेल',
  'app_password' => 'पारण शब्द',
  'app_remember_token' => 'टोकन याद रखें',
  'app_permissions' => 'अनुमतियां',
  'app_user_actions' => 'उपयोगकर्ता क्रियाएँ',
  'app_action' => 'कार्य',
  'app_action_model' => 'एक्शन मॉडल',
  'app_action_id' => 'कार्रवाई आईडी',
  'app_time' => 'पहर',
  'app_campaign' => 'अभियान',
  'app_campaigns' => 'अभियान',
  'app_description' => 'विवरण',
  'app_valid_from' => 'से मान्य',
  'app_valid_to' => 'इस तक मान्य',
  'app_discount_amount' => 'छूट राशि',
  'app_discount_percent' => 'छूट का प्रतिशत',
  'app_coupons_amount' => 'कूपन राशि',
  'app_coupons' => 'कूपन',
  'app_code' => 'कोड',
  'app_redeem_time' => 'रिडीम समय',
  'app_coupon_management' => 'कूपन प्रबंधन',
  'app_time_management' => 'समय प्रबंधन',
  'app_projects' => 'परियोजनाओं',
  'app_reports' => 'रिपोर्ट',
  'app_time_entries' => 'समय की प्रविष्टियाँ',
  'app_work_type' => 'कार्य प्रकार',
  'app_work_types' => 'काम के प्रकार',
  'app_project' => 'परियोजना',
  'app_start_time' => 'समय शुरू',
  'app_end_time' => 'अंतिम समय',
  'app_expense_category' => 'व्यय की श्रेणी',
  'app_expense_categories' => 'व्यय श्रेणियाँ',
  'app_expense_management' => 'व्यय प्रबंधन',
  'app_expenses' => 'व्यय',
  'app_expense' => 'व्यय',
  'app_entry_date' => 'प्रवेश की तिथि',
  'app_amount' => 'रकम',
  'app_income_categories' => 'आय श्रेणियों',
  'app_monthly_report' => 'मासिक रिपोर्ट',
  'app_companies' => 'कंपनियों',
  'app_company_name' => 'कंपनी का नाम',
  'app_address' => 'पता',
  'app_website' => 'वेबसाइट',
  'app_contact_management' => 'संपर्क प्रबंधन',
  'app_contacts' => 'संपर्क',
  'app_company' => 'कंपनी',
  'app_first_name' => 'पहला नाम',
  'app_last_name' => 'अंतिम नाम',
  'app_phone' => 'फ़ोन',
  'app_phone1' => 'फोन 1',
  'app_phone2' => 'फ़ोन २',
  'app_skype' => 'स्काइप',
  'app_photo' => 'फोटो (अधिकतम 8mb)',
  'app_category_name' => 'श्रेणी नाम',
  'app_product_management' => 'उत्पाद प्रबंधन',
  'app_products' => 'उत्पाद',
  'app_product_name' => 'उत्पाद का नाम',
  'app_price' => 'मूल्य',
  'app_tags' => 'टैग',
  'app_tag' => 'टैग',
  'app_photo1' => 'photo1',
  'app_photo2' => 'photo2',
  'app_photo3' => 'photo3',
  'app_calendar' => 'कैलेंडर',
  'app_statuses' => 'वे स्थितियां',
  'app_task_management' => 'कार्य प्रबंधन',
  'app_tasks' => 'कार्य',
  'app_status' => 'स्थिति',
  'app_attachment' => 'आसक्ति',
  'app_due_date' => 'नियत तारीख',
  'app_assigned_to' => 'को सौंपना',
  'app_assets' => 'संपत्ति',
  'app_asset' => 'एसेट',
  'app_serial_number' => 'क्रमांक',
  'app_location' => 'स्थान',
  'app_locations' => 'स्थान',
  'app_assigned_user' => 'सौंपा (उपयोगकर्ता)',
  'app_notes' => 'टिप्पणियाँ',
  'app_assets_history' => 'इतिहास मान लेता है',
  'app_assets_management' => 'संपत्ति प्रबंधन',
  'app_slug' => 'काउंटर',
  'app_content_management' => 'सामग्री प्रबंधन',
  'app_text' => 'टेक्स्ट',
  'app_excerpt' => 'अंश',
  'app_featured_image' => 'निरूपित चित्र',
  'app_pages' => 'पेज',
  'app_axis' => 'एक्सिस',
  'app_show' => 'प्रदर्शन',
  'app_group_by' => 'समूह द्वारा',
  'app_chart_type' => 'चार्ट प्रकार',
  'app_create_new_report' => 'नई रिपोर्ट बनाएं',
  'app_no_reports_yet' => 'अभी तक कोई रिपोर्ट नहीं।',
  'app_created_at' => 'पर बनाया गया',
  'app_updated_at' => 'पर अपडेट किया गया',
  'app_deleted_at' => 'पर हटा दिया गया',
  'app_reports_x_axis_field' => 'X- अक्ष - कृपया किसी एक तिथि / समय क्षेत्र को चुनें',
  'app_reports_y_axis_field' => 'Y- अक्ष - कृपया संख्या फ़ील्ड में से एक चुनें',
  'app_select_crud_placeholder' => 'कृपया अपना एक CRUD चुनें',
  'app_select_dt_placeholder' => 'कृपया किसी एक दिनांक / समय क्षेत्र का चयन करें',
  'app_aggregate_function_use' => 'उपयोग करने के लिए अलग कार्य',
  'app_x_axis_group_by' => 'द्वारा एक्स-अक्ष समूह',
  'app_x_axis_field' => 'एक्स-अक्ष फ़ील्ड (दिनांक / समय)',
  'app_y_axis_field' => 'Y- अक्ष क्षेत्र',
  'app_integer_float_placeholder' => 'कृपया पूर्णांक / फ्लोट क्षेत्रों में से एक का चयन करें',
  'app_change_notifications_field_1_label' => 'उपयोगकर्ता को ईमेल सूचना भेजें',
  'app_change_notifications_field_2_label' => 'जब CRUD पर प्रवेश',
  'app_select_users_placeholder' => 'कृपया अपने उपयोगकर्ताओं में से एक का चयन करें',
  'app_is_created' => 'बनाया गया है',
  'app_is_updated' => 'यह अद्यतित है',
  'app_is_deleted' => 'हटा दिया गया है',
  'app_notifications' => 'सूचनाएं',
  'app_notify_user' => 'उपयोगकर्ता को सूचित करें',
  'app_when_crud' => 'जब सी.आर.यू.डी.',
  'app_create_new_notification' => 'नई अधिसूचना बनाएँ',
  'app_stripe_transactions' => 'धारीदार लेन-देन',
  'app_upgrade_to_premium' => 'प्रीमियम में अपग्रेड करें',
  'app_messages' => 'संदेश',
  'app_you_have_no_messages' => 'आपके लिए कोई सन्देश नहीं है।',
  'app_all_messages' => 'सभी संदेश',
  'app_new_message' => 'नया संदेश',
  'app_outbox' => 'आउटबॉक्स',
  'app_inbox' => 'इनबॉक्स',
  'app_recipient' => 'प्राप्त करने वाला',
  'app_subject' => 'विषय',
  'app_message' => 'संदेश',
  'app_send' => 'भेजना',
  'app_reply' => 'जवाब दे दो',
  'app_calendar_sources' => 'कैलेंडर स्रोत',
  'app_new_calendar_source' => 'नया कैलेंडर स्रोत बनाएं',
  'app_crud_title' => 'क्रूड शीर्षक',
  'app_crud_date_field' => 'क्रूड तिथि क्षेत्र',
  'app_prefix' => 'उपसर्ग',
  'app_label_field' => 'लेबल फ़ील्ड',
  'app_suffix' => 'SUFIX',
  'app_no_calendar_sources' => 'कोई कैलेंडर स्रोत अभी तक नहीं।',
  'app_crud_event_field' => 'इवेंट लेबल फ़ील्ड',
  'app_create_new_calendar_source' => 'नया कैलेंडर स्रोत बनाएं',
  'app_edit_calendar_source' => 'कैलेंडर स्रोत संपादित करें',
  'app_client_management' => 'ग्राहक प्रबंधन',
  'app_client_management_settings' => 'ग्राहक प्रबंधन सेटिंग्स',
  'app_country' => 'देश',
  'app_client_status' => 'ग्राहक की स्थिति',
  'app_clients' => 'ग्राहकों',
  'app_client_statuses' => 'ग्राहक की स्थिति',
  'app_currencies' => 'मुद्राओं',
  'app_main_currency' => 'मुख्य मुद्रा',
  'app_documents' => 'दस्तावेज़',
  'app_file' => 'फ़ाइल',
  'app_income_source' => 'आय स्रोत',
  'app_income_sources' => 'आय के स्रोत',
  'app_fee_percent' => 'फीस प्रतिशत',
  'app_note_text' => 'नोट पाठ',
  'app_client' => 'ग्राहक',
  'app_start_date' => 'आरंभ करने की तिथि',
  'app_budget' => 'बजट',
  'app_project_status' => 'परियोजना की स्थिति',
  'app_project_statuses' => 'परियोजना की स्थिति',
  'app_transactions' => 'लेन-देन',
  'app_transaction_types' => 'लेन-देन के प्रकार',
  'app_transaction_type' => 'सौदे का प्रकार',
  'app_transaction_date' => 'लेन - देन की तारीख',
  'app_currency' => 'मुद्रा',
  'app_current_password' => 'वर्तमान पासवर्ड',
  'app_new_password' => 'नया पासवर्ड',
  'app_password_confirm' => 'नया पासवर्ड पुष्टि',
  'app_dashboard_text' => 'आप लोग्ड इन हो चुके हैं!',
  'app_forgot_password' => 'अपना कूट शब्द भूल गए?',
  'app_remember_me' => 'मुझे याद रखना',
  'app_login' => 'लॉग इन करें',
  'app_change_password' => 'पासवर्ड बदलें',
  'app_csv' => 'सीएसवी',
  'app_print' => 'छाप',
  'app_excel' => 'एक्सेल',
  'app_copy' => 'प्रतिलिपि',
  'app_colvis' => 'स्तंभ दृश्यता',
  'app_pdf' => 'पीडीएफ',
  'app_reset_password' => 'पासवर्ड रीसेट',
  'app_reset_password_woops' => '<strong>ओह!</strong> इनपुट में समस्याएं थीं:',
  'app_email_line1' => 'आप यह ईमेल प्राप्त कर रहे हैं क्योंकि हमें आपके खाते के लिए पासवर्ड रीसेट अनुरोध प्राप्त हुआ है।',
  'app_email_line2' => 'यदि आपने पासवर्ड रीसेट का अनुरोध नहीं किया है, तो आगे की कार्रवाई की आवश्यकता नहीं है।',
  'app_email_greet' => 'नमस्ते',
  'app_email_regards' => 'सादर',
  'app_confirm_password' => 'पासवर्ड की पुष्टि कीजिये',
  'app_if_you_are_having_trouble' => 'यदि आपको क्लिक करने में समस्या हो रही है',
  'app_copy_paste_url_bellow' => 'नीचे दिए गए URL को अपने वेब ब्राउज़र में बटन, कॉपी और पेस्ट करें:',
  'app_please_select' => 'कृपया चुने',
  'app_register' => 'रजिस्टर',
  'app_registration' => 'पंजीकरण',
  'app_not_approved_title' => 'आपको मंजूर नहीं है',
  'app_not_approved_p' => 'आपका खाता अभी भी व्यवस्थापक द्वारा अनुमोदित नहीं है। कृपया, धैर्य रखें और बाद में फिर से प्रयास करें।',
  'app_there_were_problems_with_input' => 'इनपुट में समस्याएं थीं',
  'app_whoops' => 'ओह!',
  'app_file_contains_header_row' => 'फ़ाइल में शीर्ष लेख पंक्ति है?',
  'app_csvImport' => 'CSV आयात',
  'app_csv_file_to_import' => 'आयात करने के लिए CSV फ़ाइल',
  'app_parse_csv' => 'पार्स सीएसवी',
  'app_import_data' => 'आयात आंकड़ा',
  'app_imported_rows_to_table' => 'आयातित: पंक्तियाँ पंक्तियाँ: तालिका तालिका',
  'app_subscription-billing' => 'सदस्यता',
  'app_subscription-payments' => 'भुगतान',
  'app_basic_crm' => 'मूल CRM',
  'app_customers' => 'ग्राहकों',
  'app_customer' => 'ग्राहक',
  'app_select_all' => 'सभी का चयन करे',
  'app_deselect_all' => 'सभी को अचिन्हिंत करें',
  'app_team-management' => 'टीमें',
  'app_team-management-singular' => 'टीम',
  'global_title' => 'डिजी लेखा, चालान और बिलिंग सीआरएम',
  'app_add_key' => 'कुंजी जोड़ें',
  'app_settings' => 'सेटिंग्स',
  'app_make_default' => 'डिफ़ॉल्ट बनाना',
  'info' => 'जानकारी',
  'operations_disabled' => 'डेमो मोड पर ऑपरेशन अक्षम',
  'download-template' => 'टेम्पलेट डाउनलोड करें',
  'app_refresh' => 'ताज़ा करना',
  'app_loading' => 'लोड हो रहा है',
);
