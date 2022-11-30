<?php

return array (
  'user-management' => 
  array (
    'title' => 'వాడుకరి నిర్వహణ',
  ),
  'permissions' => 
  array (
    'title' => 'అనుమతులు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
    ),
  ),
  'roles' => 
  array (
    'title' => 'పాత్రలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'permission' => 'అనుమతులు',
    ),
  ),
  'users' => 
  array (
    'title' => 'వినియోగదారులు',
    'active' => 'Active',
    'suspend' => 'సస్పెండ్',
    'activate' => 'సక్రియం',
    'fields' => 
    array (
      'name' => 'పేరు',
      'email' => 'ఇమెయిల్',
      'password' => 'పాస్వర్డ్',
      'role' => 'పాత్ర',
      'remember-token' => 'టోకెన్ గుర్తుంచుకో',
      'contact-reference' => 'సూచనను సంప్రదించండి',
      'department' => 'శాఖ',
      'status' => 'స్థితి',
    ),
  ),
  'user-actions' => 
  array (
    'title' => 'వాడుకరి చర్యలు',
    'created_at' => 'సమయం',
    'fields' => 
    array (
      'user' => 'వాడుకరి',
      'action' => 'యాక్షన్',
      'action-model' => 'యాక్షన్ మోడల్',
      'action-id' => 'చర్య ఐడి',
    ),
  ),
  'contact-management' => 
  array (
    'title' => 'నిర్వహణ నిర్వహణ',
  ),
  'contact-companies' => 
  array (
    'title' => 'కంపెనీలు',
    'fields' => 
    array (
      'name' => 'కంపెనీ పేరు',
      'email' => 'ఇమెయిల్',
      'address' => 'చిరునామా',
      'website' => 'వెబ్సైట్',
    ),
  ),
  'contacts' => 
  array (
    'title' => 'కాంటాక్ట్స్',
    'title_customers' => 'వినియోగదారుడు',
    'title_customer' => 'కస్టమర్',
    'title_suppliers' => 'సప్లయర్స్',
    'title_supplier' => 'సరఫరాదారు',
    'title_leads' => 'దారితీస్తుంది',
    'title_lead' => 'లీడ్',
    'title_profile' => 'ప్రొఫైల్',
    'edit-profile' => 'ప్రొఫైల్ను సవరించండి',
    'fields' => 
    array (
      'company' => 'కంపెనీ',
      'group' => 'గ్రూప్',
      'contact-type' => 'పరిచయం రకం',
      'first-name' => 'మొదటి పేరు',
      'last-name' => 'చివరి పేరు',
      'language' => 'భాషా',
      'phone1' => 'ఫోన్ 1',
      'phone2' => 'ఫోన్ 2',
      'phone1_code' => 'ఫోన్ 1 కోడ్',
      'phone2_code' => 'ఫోన్ 2 కోడ్',
      'email' => 'ఇమెయిల్',
      'skype' => 'స్కైప్',
      'address' => 'చిరునామా',
      'city' => 'సిటీ',
      'state-region' => 'రాష్ట్రం / ప్రాంతం',
      'zip-postal-code' => 'జిప్ / పోస్టల్ కోడ్',
      'tax-id' => 'పన్ను గుర్తింపు',
      'country' => 'దేశం',
    ),
  ),
  'expense-management' => 
  array (
    'title' => 'అకౌంటింగ్',
  ),
  'expense-category' => 
  array (
    'title' => 'ఖర్చు వర్గం',
    'fields' => 
    array (
      'name' => 'పేరు',
    ),
  ),
  'income-category' => 
  array (
    'title' => 'ఆదాయం వర్గాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
    ),
  ),
  'income' => 
  array (
    'title' => 'ఆదాయపు',
    'fields' => 
    array (
      'account' => 'ఖాతా',
      'income-category' => 'ఆదాయం వర్గం',
      'entry-date' => 'ఎంట్రీ తేదీ',
      'amount' => 'మొత్తం',
      'description' => 'వివరణ',
      'description-file' => 'వివరణ ఫైలు',
      'payer' => 'చెల్లింపుదారు',
      'pay-method' => 'చెల్లించండి పద్ధతి',
      'ref-no' => 'Ref #',
    ),
  ),
  'expense' => 
  array (
    'title' => 'ఖర్చులు',
    'fields' => 
    array (
      'account' => 'ఖాతా',
      'expense-category' => 'ఖర్చు వర్గం',
      'entry-date' => 'ఎంట్రీ తేదీ',
      'amount' => 'మొత్తం',
      'description' => 'వివరణ',
      'description-file' => 'వివరణ ఫైలు',
      'payee' => 'చెల్లింపును స్వీకరించే',
      'payment-method' => 'పైకము చెల్లించు విదానం',
      'ref-no' => 'Ref #',
    ),
  ),
  'monthly-report' => 
  array (
    'title' => 'మంత్లీ రిపోర్ట్',
  ),
  'faq-management' => 
  array (
    'title' => 'FAQ మేనేజ్మెంట్',
    'faq' => 'తరచుగా అడిగే ప్రశ్నలు',
  ),
  'faq-categories' => 
  array (
    'title' => 'వర్గం',
    'fields' => 
    array (
      'title' => 'వర్గం',
    ),
  ),
  'faq-questions' => 
  array (
    'title' => 'ప్రశ్నలు',
    'fields' => 
    array (
      'category' => 'వర్గం',
      'question-text' => 'ప్రశ్న',
      'answer-text' => 'సమాధానం',
    ),
  ),
  'internal-notifications' => 
  array (
    'title' => 'ప్రకటనలు',
    'fields' => 
    array (
      'text' => 'టెక్స్ట్',
      'link' => 'లింక్',
      'users' => 'వినియోగదారులు',
    ),
  ),
  'task-management' => 
  array (
    'title' => 'టాస్క్ మేనేజ్మెంట్',
  ),
  'task-statuses' => 
  array (
    'title' => 'హోదాలు',
    'panel-default' => 'డిఫాల్ట్ ప్యానెల్',
    'panel-primary' => 'ప్రాథమిక ప్యానెల్',
    'panel-success' => 'సక్సెస్ ప్యానెల్',
    'panel-info' => 'సమాచార ప్యానెల్',
    'panel-warning' => 'హెచ్చరిక ప్యానెల్',
    'panel-danger' => 'డేంజర్ ప్యానెల్',
    'fields' => 
    array (
      'name' => 'పేరు',
      'color' => 'రంగు',
    ),
  ),
  'task-tags' => 
  array (
    'title' => 'టాగ్లు',
    'fields' => 
    array (
      'name' => 'పేరు',
    ),
  ),
  'tasks' => 
  array (
    'title' => 'పనులు',
    'task-status-updated' => 'విధి స్థితి విజయవంతంగా నవీకరించబడింది',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
      'status' => 'స్థితి',
      'tag' => 'టాగ్లు',
      'attachment' => 'జోడింపు',
      'start-date' => 'ప్రారంబపు తేది',
      'due-date' => 'గడువు తేది',
      'user' => 'కేటాయించిన',
    ),
  ),
  'task-calendar' => 
  array (
    'title' => 'క్యాలెండర్',
    'status-wise' => 'స్థితి జ్ఞానం',
  ),
  'content-management' => 
  array (
    'title' => 'విషయ గ్రంథస్త నిర్వహణ',
  ),
  'content-categories' => 
  array (
    'title' => 'వర్గం',
    'fields' => 
    array (
      'title' => 'వర్గం',
      'slug' => 'స్లగ్',
    ),
  ),
  'content-tags' => 
  array (
    'title' => 'టాగ్లు',
    'fields' => 
    array (
      'title' => 'ట్యాగ్',
      'slug' => 'స్లగ్',
    ),
  ),
  'content-pages' => 
  array (
    'title' => 'పేజీలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'category-id' => 'వర్గం',
      'tag-id' => 'టాగ్లు',
      'page-text' => 'టెక్స్ట్',
      'excerpt' => 'ఎక్సెర్ప్ట్',
      'featured-image' => 'ఫీచర్ చిత్రం',
      'created-at' => 'సమయం సృష్టించబడింది',
    ),
  ),
  'product-management' => 
  array (
    'title' => 'ఉత్పత్తి నిర్వహణ',
  ),
  'product-categories' => 
  array (
    'title' => 'వర్గం',
    'fields' => 
    array (
      'name' => 'వర్గం పేరు',
      'description' => 'వివరణ',
      'photo' => 'ఫోటో (గరిష్టంగా 8mb)',
    ),
  ),
  'product-tags' => 
  array (
    'title' => 'టాగ్లు',
    'fields' => 
    array (
      'name' => 'పేరు',
    ),
  ),
  'products' => 
  array (
    'title' => 'ఉత్పత్తులు',
    'gallery-file-types' => 'అంగీకరించిన ఫైల్ రకాలు: png, jpg, jpeg, gif',
    'fields' => 
    array (
      'name' => 'ఉత్పత్తి పేరు',
      'product-code' => 'ఉత్పత్తి కోడ్',
      'actual-price' => 'అసలు ధర',
      'sale-price' => 'అమ్ముడు ధర',
      'category' => 'వర్గం',
      'tag' => 'ట్యాగ్',
      'ware-house' => 'వేర్ హౌస్',
      'description' => 'వివరణ',
      'excerpt' => 'ఎక్సెర్ప్ట్',
      'stock-quantity' => 'స్టాక్ పరిమాణం',
      'alert-quantity' => 'హెచ్చరిక పరిమాణం',
      'image-gallery' => 'చిత్రం గ్యాలరీ',
      'thumbnail' => 'సూక్ష్మచిత్రం',
      'other-files' => 'ఇతర ఫైళ్ళు',
      'hsn-sac-code' => 'HSN / SAC కోడ్',
      'product-size' => 'ఉత్పత్తి పరిమాణం',
      'product-weight' => 'ఉత్పత్తి బరువు',
      'brand' => 'బ్రాండ్',
    ),
  ),
  'assets-management' => 
  array (
    'title' => 'ఆస్తుల నిర్వహణ',
  ),
  'assets-categories' => 
  array (
    'title' => 'వర్గం',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
    ),
  ),
  'assets-statuses' => 
  array (
    'title' => 'హోదాలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
    ),
  ),
  'assets-locations' => 
  array (
    'title' => 'స్థానాలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
    ),
  ),
  'assets' => 
  array (
    'title' => 'ఆస్తులు',
    'fields' => 
    array (
      'category' => 'వర్గం',
      'serial-number' => 'క్రమ సంఖ్య',
      'title' => 'శీర్షిక',
      'photo1' => 'సూక్ష్మచిత్రం',
      'photo2' => 'గ్యాలరీ',
      'attachments' => 'అటాచ్మెంట్లు',
      'status' => 'స్థితి',
      'location' => 'స్థానం',
      'assigned-user' => 'కేటాయించిన (వినియోగదారు)',
      'notes' => 'గమనికలు',
    ),
  ),
  'assets-history' => 
  array (
    'title' => 'ఆస్తుల చరిత్ర',
    'created_at' => 'సమయం',
    'fields' => 
    array (
      'asset' => 'ఆస్తి',
      'status' => 'స్థితి',
      'location' => 'స్థానం',
      'assigned-user' => 'కేటాయించిన (వినియోగదారు)',
    ),
  ),
  'coupon-management' => 
  array (
    'title' => 'కూపన్ మేనేజ్మెంట్',
  ),
  'coupon-campaigns' => 
  array (
    'title' => 'ప్రచారాలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'description' => 'వివరణ',
      'valid-from' => 'నుండి చెల్లుబాటు అయ్యే',
      'valid-to' => 'చెల్లుతుంది',
      'discount-amount' => 'డిస్కౌంట్ మొత్తం',
      'discount-percent' => 'డిస్కౌంట్ శాతం',
      'coupons-amount' => 'కూపన్లు మొత్తం',
    ),
  ),
  'coupons' => 
  array (
    'title' => 'కూపన్లు',
    'fields' => 
    array (
      'campaign' => 'ప్రచారం',
      'code' => 'కోడ్',
      'valid-from' => 'నుండి చెల్లుబాటు అయ్యే',
      'valid-to' => 'చెల్లుతుంది',
      'discount-amount' => 'డిస్కౌంట్ మొత్తం',
      'discount-percent' => 'డిస్కౌంట్ శాతం',
      'redeem-time' => 'సమయాన్ని ఆదా చేయండి',
    ),
  ),
  'expense-types' => 
  array (
    'title' => 'ఖర్చు రకాలు',
  ),
  'global-settings' => 
  array (
    'title' => 'గ్లోబల్ సెట్టింగ్లు',
  ),
  'currencies' => 
  array (
    'title' => 'కరెన్సీలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'symbol' => 'చిహ్నం',
      'code' => 'కోడ్',
      'rate' => 'రేటు',
      'status' => 'స్థితి',
      'is_default' => 'డిఫాల్ట్?',
    ),
  ),
  'sales-taxes' => 
  array (
    'title' => 'సేల్స్ టాక్స్',
  ),
  'email-templates' => 
  array (
    'title' => 'ఇమెయిల్ టెంప్లేట్లను',
    'fields' => 
    array (
      'name' => 'పేరు',
      'subject' => 'Subject',
      'body' => 'శరీర',
    ),
  ),
  'companies' => 
  array (
    'title' => 'కంపెనీలు',
    'fields' => 
    array (
      'company-name' => 'కంపెనీ పేరు',
      'address' => 'చిరునామా',
      'business-number' => 'వ్యాపారం సంఖ్య',
      'city' => 'సిటీ',
      'url' => 'url',
      'state-region' => 'రాష్ట్రం / ప్రాంతం',
      'email' => 'ఇమెయిల్',
      'zip-postal-code' => 'జిప్ / పోస్టల్ కోడ్',
      'country' => 'దేశం',
      'phone' => 'ఫోన్',
      'logo' => 'లోగో',
    ),
  ),
  'accounts' => 
  array (
    'title' => 'అకౌంట్స్',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
      'initial-balance' => 'ప్రారంభ సంతులనం',
      'account-number' => 'ఖాతా సంఖ్య',
      'contact-person' => 'వ్యక్తి సంప్రదించండి',
      'phone' => 'ఫోన్',
      'url' => 'URL',
    ),
  ),
  'payment-gateways' => 
  array (
    'title' => 'చెల్లింపు ముఖద్వారాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
      'logo' => 'లోగో',
    ),
  ),
  'warehouses' => 
  array (
    'title' => 'గిడ్డంగులు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'address' => 'చిరునామా',
      'description' => 'వివరణ',
    ),
  ),
  'taxes' => 
  array (
    'title' => 'పన్నులు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'rate' => 'రేటు',
      'rate-type' => 'రేట్ రకం',
      'description' => 'వివరణ',
    ),
  ),
  'discounts' => 
  array (
    'title' => 'డిస్కౌంట్',
    'fields' => 
    array (
      'name' => 'పేరు',
      'discount' => 'డిస్కౌంట్',
      'discount-type' => 'డిస్కౌంట్ రకం',
      'description' => 'వివరణ',
    ),
  ),
  'recurring-periods' => 
  array (
    'title' => 'పునరావృత కాలాలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'value' => 'విలువ',
      'description' => 'వివరణ',
    ),
  ),
  'languages' => 
  array (
    'title' => 'భాషలు',
    'fields' => 
    array (
      'language' => 'భాషా',
      'code' => 'కోడ్',
      'is-rtl' => 'RTL ఉంది',
    ),
  ),
  'purchase-orders' => 
  array (
    'title' => 'ఆర్డర్లను కొనుగోలు చేయండి',
    'fields' => 
    array (
      'customer' => 'సరఫరాదారు',
      'subject' => 'Subject',
      'status' => 'స్థితి',
      'address' => 'చిరునామా',
      'invoice-prefix' => 'వాయిస్ ఉపసర్గ',
      'show-quantity-as' => 'పరిమాణం చూపించు',
      'invoice-no' => 'ఇన్వాయిస్ #',
      'reference' => 'సూచన',
      'order-date' => 'జారి చెయ్యబడిన చోటు',
      'order-due-date' => 'ఆర్డర్ గడువు తేదీ',
      'update-stock' => 'స్టాక్ను నవీకరించండి',
      'notes' => 'గమనికలు',
      'currency' => 'కరెన్సీ',
      'warehouse' => 'వేర్హౌస్',
      'tax' => 'పన్ను',
      'discount' => 'డిస్కౌంట్',
      'amount' => 'మొత్తం',
    ),
  ),
  'manage-projects' => 
  array (
    'title' => 'ప్రాజెక్టులను నిర్వహించండి',
  ),
  'projects' => 
  array (
    'title' => 'ప్రాజెక్ట్స్',
  ),
  'project-statuses' => 
  array (
    'title' => 'ప్రాజెక్ట్ హోదాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
    ),
  ),
  'client-projects' => 
  array (
    'title' => 'క్లయింట్ ప్రాజెక్టులు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'client' => 'క్లయింట్',
      'priority' => 'ప్రాధాన్యత',
      'budget' => 'బడ్జెట్',
      'billing-type' => 'బిల్లింగ్ రకం',
      'phase' => 'దశ',
      'assigned-to' => 'కేటాయించిన',
      'start-date' => 'ప్రారంబపు తేది',
      'due-date' => 'గడువు తేది',
      'status' => 'స్థితి',
      'description' => 'వివరణ',
      'demo-url' => 'డెమో url',
    ),
  ),
  'project-billing-types' => 
  array (
    'title' => 'ప్రాజెక్ట్ బిల్లింగ్ రకాలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'description' => 'వివరణ',
    ),
  ),
  'time-entries' => 
  array (
    'title' => 'సమయం ఎంట్రీలు',
    'fields' => 
    array (
      'project' => 'ప్రాజెక్ట్',
      'start-date' => 'ప్రారంబపు తేది',
      'end-date' => 'ఆఖరి తేది',
      'description' => 'వివరణ',
    ),
  ),
  'sales' => 
  array (
    'title' => 'అమ్మకాలు',
  ),
  'invoices' => 
  array (
    'title' => 'రసీదులు',
    'fields' => 
    array (
      'customer' => 'కస్టమర్',
      'currency' => 'కరెన్సీ',
      'title' => 'శీర్షిక',
      'address' => 'చిరునామా',
      'invoice-prefix' => 'వాయిస్ ఉపసర్గ',
      'show-quantity-as' => 'పరిమాణం చూపించు',
      'invoice-no' => 'ఇన్వాయిస్ #',
      'status' => 'స్థితి',
      'reference' => 'సూచన',
      'invoice-date' => 'చలానా తారీకు',
      'invoice-due-date' => 'వాయిస్ గడువు తేదీ',
      'invoice-notes' => 'వాయిస్ గమనికలు',
      'tax' => 'పన్ను',
      'discount' => 'డిస్కౌంట్',
      'amount' => 'మొత్తం',
      'discount_format' => 'డిస్కౌంట్ ఫార్మాట్',
      'tax_format' => 'పన్ను ఫార్మాట్',
    ),
  ),
  'quotes' => 
  array (
    'title' => 'వ్యాఖ్యలు',
    'fields' => 
    array (
      'customer' => 'కస్టమర్',
      'status' => 'స్థితి',
      'title' => 'శీర్షిక',
      'address' => 'చిరునామా',
      'quote-prefix' => 'కోట్ ఉపసర్గ',
      'show-quantity-as' => 'పరిమాణం చూపించు',
      'quote-no' => 'కోట్ లేదు',
      'reference' => 'సూచన',
      'quote-date' => 'కోట్ తేదీ',
      'quote-expiry-date' => 'కోట్ గడువు తేదీ',
      'proposal-text' => 'ప్రతిపాదన టెక్స్ట్',
      'currency' => 'కరెన్సీ',
      'tax' => 'పన్ను',
      'discount' => 'డిస్కౌంట్',
      'amount' => 'మొత్తం',
    ),
  ),
  'recurring-invoices' => 
  array (
    'title' => 'పునరావృత ఇన్వాయిస్లు',
    'fields' => 
    array (
      'customer' => 'కస్టమర్',
      'currency' => 'కరెన్సీ',
      'title' => 'శీర్షిక',
      'address' => 'చిరునామా',
      'invoice-prefix' => 'వాయిస్ ఉపసర్గ',
      'show-quantity-as' => 'పరిమాణం చూపించు',
      'invoice-no' => 'ఇన్వాయిస్ #',
      'status' => 'స్థితి',
      'reference' => 'సూచన',
      'invoice-date' => 'చలానా తారీకు',
      'invoice-due-date' => 'వాయిస్ గడువు తేదీ',
      'invoice-notes' => 'వాయిస్ గమనికలు',
      'tax' => 'పన్ను',
      'discount' => 'డిస్కౌంట్',
      'recurring-period' => 'పునరావృత కాలం',
      'amount' => 'మొత్తం',
      'products' => 'ఉత్పత్తులు',
      'paymentstatus' => 'చెల్లింపు స్థితి',
    ),
  ),
  'contact-groups' => 
  array (
    'title' => 'పరిచయ సమూహాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
    ),
  ),
  'contact-types' => 
  array (
    'title' => 'సంప్రదించండి రకాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
    ),
  ),
  'contact-notes' => 
  array (
    'title' => 'సంప్రదింపు గమనికలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'contact' => 'సంప్రదించండి',
      'notes' => 'గమనికలు',
      'attachment' => 'జోడింపు',
    ),
  ),
  'contact-documents' => 
  array (
    'title' => 'సంప్రదించండి పత్రాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
      'attachments' => 'అటాచ్మెంట్లు',
      'contact' => 'సంప్రదించండి',
    ),
  ),
  'products-transfer' => 
  array (
    'title' => 'ఉత్పత్తులు బదిలీ',
  ),
  'products-return' => 
  array (
    'title' => 'ఉత్పత్తులు తిరిగి వస్తాయి',
    'fields' => 
    array (
      'subject' => 'Subject',
      'customer' => 'కస్టమర్',
      'currency' => 'కరెన్సీ',
      'status' => 'స్థితి',
      'address' => 'చిరునామా',
      'invoice-prefix' => 'వాయిస్ ఉపసర్గ',
      'show-quantity-as' => 'పరిమాణం చూపించు',
      'invoice-no' => 'ఇన్వాయిస్ #',
      'reference' => 'సూచన',
      'order-date' => 'ఆర్డర్ తేదీ',
      'order-due-date' => 'ఆర్డర్ గడువు తేదీ',
      'update-stock' => 'స్టాక్ను నవీకరించండి',
      'notes' => 'గమనికలు',
      'tax' => 'పన్ను',
      'discount' => 'డిస్కౌంట్',
      'ware-house' => 'వేర్ హౌస్',
    ),
  ),
  'brands' => 
  array (
    'title' => 'బ్రాండ్స్',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'icon' => 'ఐకాన్',
      'status' => 'స్థితి',
    ),
  ),
  'database-backup' => 
  array (
    'title' => 'డేటాబేస్ బ్యాకప్',
  ),
  'departments' => 
  array (
    'title' => 'విభాగాలు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'description' => 'వివరణ',
      'created-by' => 'సృష్టికర్త',
    ),
  ),
  'support' => 
  array (
    'title' => 'మద్దతు',
    'fields' => 
    array (
      'name' => 'పేరు',
      'email' => 'ఇమెయిల్',
      'subject' => 'Subject',
      'department' => 'శాఖ',
      'priority' => 'ప్రాధాన్యత',
      'description' => 'వివరణ',
      'attachments' => 'అటాచ్మెంట్లు',
      'created-by' => 'సృష్టికర్త',
      'assigned-to' => 'కేటాయించిన',
    ),
  ),
  'knowledgebase' => 
  array (
    'title' => 'ఆధారితం',
  ),
  'transfers' => 
  array (
    'title' => 'బదిలీలు',
    'fields' => 
    array (
      'from' => 'నుండి',
      'to' => 'టు',
      'date' => 'తేదీ',
      'amount' => 'మొత్తం',
      'ref-no' => 'Ref #',
      'payment-method' => 'పైకము చెల్లించు విదానం',
      'description' => 'వివరణ',
    ),
  ),
  'articles' => 
  array (
    'title' => 'వ్యాసాలు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'category-id' => 'వర్గం',
      'tag-id' => 'టాగ్లు',
      'page-text' => 'టెక్స్ట్',
      'excerpt' => 'ఎక్సెర్ప్ట్',
      'featured-image' => 'ఫీచర్ చిత్రం',
      'available-for' => 'అందుబాటులో',
    ),
  ),
  'balance-sheet' => 
  array (
    'title' => 'బ్యాలెన్స్ షీట్',
  ),
  'general-settings' => 
  array (
    'title' => 'సాధారణ సెట్టింగులు',
  ),
  'master-settings' => 
  array (
    'title' => 'మాస్టర్ సెట్టింగులు',
    'fields' => 
    array (
      'module' => 'మాడ్యూల్',
      'key' => 'కీ',
      'description' => 'వివరణ',
    ),
  ),
  'countries' => 
  array (
    'title' => 'దేశాలు',
    'fields' => 
    array (
      'shortcode' => 'చిన్న కోడ్',
      'title' => 'శీర్షిక',
    ),
  ),
  'measurement-units' => 
  array (
    'title' => 'కొలత యూనిట్లు',
    'fields' => 
    array (
      'title' => 'శీర్షిక',
      'status' => 'స్థితి',
      'description' => 'వివరణ',
    ),
  ),
  'payments' => 
  array (
    'title' => 'చెల్లింపులు',
    'failed' => 'చెల్లింపు విఫలమైంది',
    'cancelled' => 'మీరు మీ చెల్లింపును రద్దు చేసారు',
  ),
  'navigation-menues' => 
  array (
    'title' => 'నావిగేషన్ మెన్యుస్',
  ),
  'app_create' => 'సృష్టించు',
  'app_save' => 'సేవ్',
  'app_edit' => 'మార్చు',
  'app_restore' => 'పునరుద్ధరించు',
  'app_values' => 'విలువలు',
  'app_permadel' => 'శాశ్వతంగా తొలగించు',
  'app_all' => 'అన్ని',
  'app_trash' => 'ట్రాష్',
  'app_view' => 'చూడండి',
  'app_update' => 'నవీకరణ',
  'app_list' => 'జాబితా',
  'app_no_entries_in_table' => 'పట్టికలో ఎంట్రీలు లేవు',
  'app_custom_controller_index' => 'కస్టమ్ కంట్రోలర్ సూచిక.',
  'app_logout' => 'లాగౌట్',
  'app_add_new' => 'కొత్తది జత పరచండి',
  'app_are_you_sure' => 'మీరు చెప్పేది నిజమా?',
  'app_back_to_list' => 'జాబితాకు తిరిగి వెళ్ళు',
  'app_dashboard' => 'డాష్బోర్డ్',
  'app_delete' => 'తొలగించు',
  'app_delete_selected' => 'ఎంచుకున్నవాటిని రద్దు చేయుట',
  'app_category' => 'వర్గం',
  'app_categories' => 'వర్గం',
  'app_sample_category' => 'నమూనా వర్గం',
  'app_questions' => 'ప్రశ్నలు',
  'app_question' => 'ప్రశ్న',
  'app_answer' => 'సమాధానం',
  'app_sample_question' => 'నమూనా ప్రశ్న',
  'app_sample_answer' => 'నమూనా సమాధానం',
  'app_faq_management' => 'FAQ మేనేజ్మెంట్',
  'app_administrator_can_create_other_users' => 'నిర్వాహకుడు (ఇతర వినియోగదారులు సృష్టించగలరు)',
  'app_simple_user' => 'సాధారణ యూజర్',
  'app_title' => 'శీర్షిక',
  'app_roles' => 'పాత్రలు',
  'app_role' => 'పాత్ర',
  'app_user_management' => 'వాడుకరి నిర్వహణ',
  'app_users' => 'వినియోగదారులు',
  'app_user' => 'వాడుకరి',
  'app_name' => 'పేరు',
  'app_email' => 'ఇమెయిల్',
  'app_password' => 'పాస్వర్డ్',
  'app_remember_token' => 'టోకెన్ గుర్తుంచుకో',
  'app_permissions' => 'అనుమతులు',
  'app_user_actions' => 'వాడుకరి చర్యలు',
  'app_action' => 'యాక్షన్',
  'app_action_model' => 'యాక్షన్ మోడల్',
  'app_action_id' => 'చర్య ఐడి',
  'app_time' => 'సమయం',
  'app_campaign' => 'ప్రచారం',
  'app_campaigns' => 'ప్రచారాలు',
  'app_description' => 'వివరణ',
  'app_valid_from' => 'నుండి చెల్లుబాటు అయ్యే',
  'app_valid_to' => 'చెల్లుతుంది',
  'app_discount_amount' => 'డిస్కౌంట్ మొత్తం',
  'app_discount_percent' => 'డిస్కౌంట్ శాతం',
  'app_coupons_amount' => 'కూపన్లు మొత్తం',
  'app_coupons' => 'కూపన్లు',
  'app_code' => 'కోడ్',
  'app_redeem_time' => 'సమయాన్ని ఆదా చేయండి',
  'app_coupon_management' => 'కూపన్ మేనేజ్మెంట్',
  'app_time_management' => 'సమయం నిర్వహణ',
  'app_projects' => 'ప్రాజెక్ట్స్',
  'app_reports' => 'నివేదికలు',
  'app_time_entries' => 'సమయం ఎంట్రీలు',
  'app_work_type' => 'పని రకం',
  'app_work_types' => 'పని రకాలు',
  'app_project' => 'ప్రాజెక్ట్',
  'app_start_time' => 'సమయం ప్రారంభించండి',
  'app_end_time' => 'ముగింపు సమయం',
  'app_expense_category' => 'ఖర్చు వర్గం',
  'app_expense_categories' => 'ఖర్చు వర్గం',
  'app_expense_management' => 'ఖర్చు నిర్వహణ',
  'app_expenses' => 'ఖర్చులు',
  'app_expense' => 'ఖర్చుల',
  'app_entry_date' => 'ఎంట్రీ తేదీ',
  'app_amount' => 'మొత్తం',
  'app_income_categories' => 'ఆదాయం వర్గాలు',
  'app_monthly_report' => 'మంత్లీ రిపోర్ట్',
  'app_companies' => 'కంపెనీలు',
  'app_company_name' => 'కంపెనీ పేరు',
  'app_address' => 'చిరునామా',
  'app_website' => 'వెబ్సైట్',
  'app_contact_management' => 'నిర్వహణ నిర్వహణ',
  'app_contacts' => 'కాంటాక్ట్స్',
  'app_company' => 'కంపెనీ',
  'app_first_name' => 'మొదటి పేరు',
  'app_last_name' => 'చివరి పేరు',
  'app_phone' => 'ఫోన్',
  'app_phone1' => 'ఫోన్ 1',
  'app_phone2' => 'ఫోన్ 2',
  'app_skype' => 'స్కైప్',
  'app_photo' => 'ఫోటో (గరిష్టంగా 8mb)',
  'app_category_name' => 'వర్గం పేరు',
  'app_product_management' => 'ఉత్పత్తి నిర్వహణ',
  'app_products' => 'ఉత్పత్తులు',
  'app_product_name' => 'ఉత్పత్తి పేరు',
  'app_price' => 'ధర',
  'app_tags' => 'టాగ్లు',
  'app_tag' => 'ట్యాగ్',
  'app_photo1' => 'Photo1',
  'app_photo2' => 'Photo2',
  'app_photo3' => 'Photo3',
  'app_calendar' => 'క్యాలెండర్',
  'app_statuses' => 'హోదాలు',
  'app_task_management' => 'టాస్క్ మేనేజ్మెంట్',
  'app_tasks' => 'పనులు',
  'app_status' => 'స్థితి',
  'app_attachment' => 'జోడింపు',
  'app_due_date' => 'గడువు తేది',
  'app_assigned_to' => 'కేటాయించిన',
  'app_assets' => 'ఆస్తులు',
  'app_asset' => 'ఆస్తి',
  'app_serial_number' => 'క్రమ సంఖ్య',
  'app_location' => 'స్థానం',
  'app_locations' => 'స్థానాలు',
  'app_assigned_user' => 'కేటాయించిన (వినియోగదారు)',
  'app_notes' => 'గమనికలు',
  'app_assets_history' => 'ఆస్తుల చరిత్ర',
  'app_assets_management' => 'ఆస్తుల నిర్వహణ',
  'app_slug' => 'స్లగ్',
  'app_content_management' => 'విషయ గ్రంథస్త నిర్వహణ',
  'app_text' => 'టెక్స్ట్',
  'app_excerpt' => 'ఎక్సెర్ప్ట్',
  'app_featured_image' => 'ఫీచర్ చిత్రం',
  'app_pages' => 'పేజీలు',
  'app_axis' => 'యాక్సిస్',
  'app_show' => 'షో',
  'app_group_by' => 'ద్వారా సమూహం',
  'app_chart_type' => 'చార్ట్ రకం',
  'app_create_new_report' => 'కొత్త నివేదికను సృష్టించండి',
  'app_no_reports_yet' => 'ఇంకా నివేదికలు లేవు.',
  'app_created_at' => 'వద్ద సృష్టించబడింది',
  'app_updated_at' => 'వద్ద నవీకరించబడింది',
  'app_deleted_at' => 'వద్ద తొలగించబడింది',
  'app_reports_x_axis_field' => 'X- అక్షం - దయచేసి తేదీ / సమయ ఖాళీలను ఒకటి ఎంచుకోండి',
  'app_reports_y_axis_field' => 'Y- యాక్సిస్ - దయచేసి సంఖ్య ఫీల్డ్లలో ఒకదాన్ని ఎంచుకోండి',
  'app_select_crud_placeholder' => 'దయచేసి మీ CRUD లలో ఒకదాన్ని ఎంచుకోండి',
  'app_select_dt_placeholder' => 'దయచేసి తేదీ / సమయ ఫీల్డ్లలో ఒకటి ఎంచుకోండి',
  'app_aggregate_function_use' => 'ఉపయోగించడానికి మొత్తం ఫంక్షన్',
  'app_x_axis_group_by' => 'X- అక్షం సమూహం',
  'app_x_axis_field' => 'X- అక్షం క్షేత్రం (తేదీ / సమయం)',
  'app_y_axis_field' => 'Y- అక్షం ఫీల్డ్',
  'app_integer_float_placeholder' => 'దయచేసి పూర్ణాంక / ఫ్లోట్ ఫీల్డ్లలో ఒకదాన్ని ఎంచుకోండి',
  'app_change_notifications_field_1_label' => 'వినియోగదారుకు ఇమెయిల్ నోటిఫికేషన్ను పంపండి',
  'app_change_notifications_field_2_label' => 'CRUD ఎంట్రీ చేసినప్పుడు',
  'app_select_users_placeholder' => 'దయచేసి మీ వినియోగదారుల్లో ఒకదాన్ని ఎంచుకోండి',
  'app_is_created' => 'సృష్టించబడింది',
  'app_is_updated' => 'నవీకరించబడింది',
  'app_is_deleted' => 'తొలగించబడుతుంది',
  'app_notifications' => 'ప్రకటనలు',
  'app_notify_user' => 'వినియోగదారుని తెలియజేయండి',
  'app_when_crud' => 'ఎప్పుడు CRUD',
  'app_create_new_notification' => 'కొత్త నోటిఫికేషన్ సృష్టించండి',
  'app_stripe_transactions' => 'గీత లావాదేవీలు',
  'app_upgrade_to_premium' => 'ప్రీమియంకు అప్గ్రేడ్ చేయండి',
  'app_messages' => 'సందేశాలు',
  'app_you_have_no_messages' => 'మీకు సందేశాలు లేవు.',
  'app_all_messages' => 'అన్ని సందేశాలు',
  'app_new_message' => 'కొత్త సందేశం',
  'app_outbox' => 'ఔట్ బాక్స్',
  'app_inbox' => 'ఇన్బాక్స్',
  'app_recipient' => 'గ్రహీత',
  'app_subject' => 'Subject',
  'app_message' => 'సందేశం',
  'app_send' => 'పంపండి',
  'app_reply' => 'ప్రత్యుత్తరం',
  'app_calendar_sources' => 'క్యాలెండర్ మూలాలు',
  'app_new_calendar_source' => 'క్రొత్త క్యాలెండర్ మూలాన్ని సృష్టించండి',
  'app_crud_title' => 'క్రుడ్ టైటిల్',
  'app_crud_date_field' => 'క్రుడ్ డే ఫీల్డ్',
  'app_prefix' => 'ఉపసర్గ',
  'app_label_field' => 'లేబుల్ ఫీల్డ్',
  'app_suffix' => 'Sufix',
  'app_no_calendar_sources' => 'ఇంకా క్యాలెండర్ వనరులు లేవు.',
  'app_crud_event_field' => 'ఈవెంట్ లేబుల్ ఫీల్డ్',
  'app_create_new_calendar_source' => 'కొత్త క్యాలెండర్ మూలాన్ని సృష్టించండి',
  'app_edit_calendar_source' => 'క్యాలెండర్ మూలాన్ని సవరించండి',
  'app_client_management' => 'క్లయింట్ నిర్వహణ',
  'app_client_management_settings' => 'క్లయింట్ నిర్వహణ సెట్టింగులు',
  'app_country' => 'దేశం',
  'app_client_status' => 'క్లయింట్ స్థితి',
  'app_clients' => 'క్లయింట్లు',
  'app_client_statuses' => 'క్లయింట్ స్థాయిలు',
  'app_currencies' => 'కరెన్సీలు',
  'app_main_currency' => 'ప్రధాన కరెన్సీ',
  'app_documents' => 'పత్రాలు',
  'app_file' => 'ఫైలు',
  'app_income_source' => 'ఆదాయం మూలం',
  'app_income_sources' => 'ఆదాయం మూలాల',
  'app_fee_percent' => 'ఫీజు శాతం',
  'app_note_text' => 'వచనాన్ని గమనించండి',
  'app_client' => 'క్లయింట్',
  'app_start_date' => 'ప్రారంబపు తేది',
  'app_budget' => 'బడ్జెట్',
  'app_project_status' => 'ప్రాజెక్ట్ స్థితి',
  'app_project_statuses' => 'ప్రాజెక్ట్ హోదాలు',
  'app_transactions' => 'ట్రాన్సాక్షన్స్',
  'app_transaction_types' => 'లావాదేవీ రకాలు',
  'app_transaction_type' => 'లావాదేవీ రకం',
  'app_transaction_date' => 'లావాదేవీ తేదీ',
  'app_currency' => 'కరెన్సీ',
  'app_current_password' => 'ప్రస్తుత పాస్వర్డ్',
  'app_new_password' => 'కొత్త పాస్వర్డ్',
  'app_password_confirm' => 'కొత్త పాస్వర్డ్ నిర్ధారణ',
  'app_dashboard_text' => 'మీరు లాగిన్ అయ్యారు!',
  'app_forgot_password' => 'మీ పాస్వర్డ్ను మర్చిపోయారా?',
  'app_remember_me' => 'నన్ను గుర్తు పెట్టుకో',
  'app_login' => 'లాగిన్',
  'app_change_password' => 'పాస్ వర్డ్ ను మార్చండి',
  'app_csv' => 'CSV',
  'app_print' => 'ప్రింట్',
  'app_excel' => 'Excel',
  'app_copy' => 'కాపీ',
  'app_colvis' => 'కాలమ్ దృశ్యమానత',
  'app_pdf' => 'PDF',
  'app_reset_password' => 'రహస్యపదాన్ని మార్చుకోండి',
  'app_reset_password_woops' => '<strong>అయ్యో!</strong> ఇన్పుట్ తో సమస్యలు ఉన్నాయి:',
  'app_email_line1' => 'మీ ఖాతా కోసం పాస్వర్డ్ రీసెట్ అభ్యర్ధనను మేము స్వీకరించినందున మీరు ఈ ఇమెయిల్ని స్వీకరిస్తున్నారు.',
  'app_email_line2' => 'మీరు పాస్వర్డ్ రీసెట్ను అభ్యర్థించకుంటే, తదుపరి చర్య అవసరం లేదు.',
  'app_email_greet' => 'హలో',
  'app_email_regards' => 'గౌరవంతో',
  'app_confirm_password' => 'పాస్వర్డ్ని నిర్ధారించండి',
  'app_if_you_are_having_trouble' => 'మీరు క్లిక్ చేయడం సమస్య ఉంటే',
  'app_copy_paste_url_bellow' => 'బటన్, దిగువ URL ను మీ వెబ్ బ్రౌజర్లో కాపీ చేసి అతికించండి:',
  'app_please_select' => 'దయచేసి ఎంచుకోండి',
  'app_register' => 'నమోదు',
  'app_registration' => 'నమోదు',
  'app_not_approved_title' => 'మీరు ఆమోదించబడలేదు',
  'app_not_approved_p' => 'మీ ఖాతా ఇప్పటికీ నిర్వాహకునిచే ఆమోదించబడలేదు. దయచేసి, ఓపికపట్టండి మరియు తర్వాత మళ్లీ ప్రయత్నించండి.',
  'app_there_were_problems_with_input' => 'ఇన్పుట్ తో సమస్యలు ఉన్నాయి',
  'app_whoops' => 'అయ్యో!',
  'app_file_contains_header_row' => 'ఫైల్ శీర్షిక వరుసను కలిగి ఉంది?',
  'app_csvImport' => 'CSV దిగుమతి',
  'app_csv_file_to_import' => 'CSV ఫైల్ దిగుమతి',
  'app_parse_csv' => 'PARS CSV',
  'app_import_data' => 'డేటాను దిగుమతి చేయండి',
  'app_imported_rows_to_table' => 'దిగుమతి చేయబడినవి: వరుసలు వరుసలు: పట్టిక పట్టిక',
  'app_subscription-billing' => 'చందాలు',
  'app_subscription-payments' => 'చెల్లింపులు',
  'app_basic_crm' => 'ప్రాథమిక CRM',
  'app_customers' => 'వినియోగదారుడు',
  'app_customer' => 'కస్టమర్',
  'app_select_all' => 'అన్ని ఎంచుకోండి',
  'app_deselect_all' => 'అన్ని ఎంపికలను తీసివేయండి',
  'app_team-management' => 'జట్లు',
  'app_team-management-singular' => 'జట్టు',
  'global_title' => 'డిజి ఖాతా, ఇన్వాయిస్ &amp; బిల్లింగ్ CRM',
  'app_add_key' => 'కీని జోడించు',
  'app_settings' => 'సెట్టింగులు',
  'app_make_default' => 'డిఫాల్ట్గా చేయండి',
  'info' => 'సమాచారం',
  'operations_disabled' => 'ఆపరేషన్ మోడ్లో డిసేబుల్ చెయ్యబడింది',
  'download-template' => 'మూసను డౌన్లోడ్ చేయండి',
  'app_refresh' => 'రిఫ్రెష్',
  'app_loading' => 'లోడ్',
);
