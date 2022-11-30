<?php
function flashMessage( $type = 'success', $operation = 'create', $message = '' ) {
  if ( empty( $message ) ) {
     switch ( $operation ) {
        case 'create':
          $message = trans( 'custom.messages.record_saved' );
          break;
        case 'restore':
          $message = trans( 'custom.messages.record_restored' );
          break;
        case 'update':
          $message = trans( 'custom.messages.record_updated' );
          break;
        case 'delete':
          $message = trans( 'custom.messages.record_deleted' );
          break;
        case 'deletes':
          $message = trans( 'custom.messages.records_deleted' );
          break;
        case 'crud_disabled':
          $message = trans( 'custom.messages.crud_disabled' );
          break;
        case 'products_transfered':
          $message = trans( 'custom.products-transfer.transfered' );
          break;
        case 'status':
          $message = trans( 'custom.messages.status-changed' );
          break;
        case 'not_allowed':
        case 'not-allowed':
          $message = trans( 'custom.messages.not_allowed' );
          break;
        case 'not_found':
          $message = trans( 'custom.messages.not_found' );
          break;
        case 'time_out':
          $message = trans( 'custom.messages.time_out' );
          break;
        case 'invalid_record':
          $message = trans('custom.messages.invalid_record');
          break;
        case 'exception':
          $message = trans('custom.messages.delete_exception');
          break;
        case 'reset':
          $message = trans('custom.messages.reset');
          break;
        default:
          $message = trans( 'custom.messages.record_saved' );
          break;
      }
  }
  session()->flash('status', $type );
  session()->flash('message', $message );
}


function flash( $type = 'success', $message = '', $info = '' ) {
  if ( empty( $message ) ) {
     switch ( $operation ) {
        case 'create':
          $message = trans( 'custom.messages.record_saved' );
          break;
        case 'restore':
          $message = trans( 'custom.messages.record_restored' );
          break;
        case 'update':
          $message = trans( 'custom.messages.record_updated' );
          break;
        case 'delete':
          $message = trans( 'custom.messages.record_deleted' );
          break;
        case 'deletes':
          $message = trans( 'custom.messages.records_deleted' );
          break;
        case 'crud_disabled':
          $message = trans( 'custom.messages.crud_disabled' );
          break;
        case 'products_transfered':
          $message = trans( 'custom.products-transfer.transfered' );
          break;
        case 'status':
          $message = trans( 'custom.messages.status-changed' );
          break;
        case 'not_allowed':
          $message = trans( 'custom.messages.not-allowed' );
          break;
        default:
          $message = trans( 'custom.messages.record_saved' );
          break;
      }
  }
  session()->flash('status', $type );
  session()->flash('message', $message );
}

function getDefaultCurrency( $type = 'symbol', $customer_id = '' ) { 
  
  if ( ! isAdmin() && ! isExecutive() ) {
    $customer_id = Auth::id();
  }
  $symbol = getLocalSetting('default_currency', 'symbol');
  $display_currency = App\Settings::getSetting('display_currency', 'currency_settings', 'symbol');
  if( 'id' === $type ) {
    $symbol = getLocalSetting('default_currency', 'id', DEFAULT_CURRENCY_ID);
  } elseif ( ( 'code' === $display_currency ) || ( 'code' === $type ) ) {
    $symbol = getLocalSetting('default_currency', 'code', '$');
  }

  if ( ! empty( $customer_id ) ) {
    $contact = \App\Contact::where( 'id', $customer_id)->first();
    if ( $contact->currency ) {
        $currency = $contact->currency; 
        $symbol = $currency->symbol;
        if( 'id' === $type ) {
          $symbol = $currency->id;
        } elseif ( ( 'code' === $display_currency ) || ( 'code' === $type ) ) {
          $symbol = $currency->code;
        }        
    }
  }
  return $symbol;
}

function getCurrency( $id, $field = '' ) {
  
  $currency = getLocalSetting('currencies', $id);
  
  if( ! $currency ) {
    if ( is_numeric( $id ) ) {
      $currency = \App\Currency::find( $id );
    } else {
      $currency = \App\Currency::where( 'code', $id)->first();
    }
  } else {
    $currency = (Object) $currency;
  }
  
  if ( $currency ) {
    if ($currency instanceof Illuminate\Database\Eloquent\Collection) {
      $newcurrency = $currency->pop();
    } else {
      $newcurrency = $currency;
    }
    return $newcurrency->{$field};
  } else {
    $currency = getDefaultCurrency();
  }
  return $currency;
}

function getCurrencyPosition() {
  return App\Settings::getSetting('currency_position', 'currency_settings');
}

function digiCurrency( $amount, $default = '', $withcurrency = true ) {
  
  $currency_postion = getCurrencyPosition();

  $toundsand_separator = App\Settings::getSetting('toundsand_separator', 'currency_settings');
  if ( empty( $toundsand_separator ) ) {
    $toundsand_separator = ',';
  }
  $decimal_separator = App\Settings::getSetting('decimal_separator', 'currency_settings');
  if ( empty( $toundsand_separator ) ) {
    $toundsand_separator = '.';
  }
  $decimals = App\Settings::getSetting('decimals', 'currency_settings');
  if ( empty( $decimals ) ) {
    $decimals = '2';
  }

  $amount = number_format( $amount, $decimals, $decimal_separator, $toundsand_separator);

  if ( $withcurrency ) {
    $currency_position = getCurrencyPosition();

    $currency = getCurrency( $default, 'symbol' );
    
    
    if ( 'left' === $currency_position ) {
      $amount = $currency . $amount;
    }
    if ( 'right' === $currency_position ) {
      $amount = $amount . $currency;
    }
    if ( 'left_with_space' === $currency_position ) {
      $amount = $currency . ' ' . $amount;
    }
    if ( 'right_with_space' === $currency_position ) {
      $amount = $amount . ' ' . $currency;
    }
  }

  return $amount;
}

 /**
 * This method fetches the specified key in the type of setting
 * @param  [type] $key          [description]
 * @param  [type] $setting_type [description]
 * @return [type]               [description]
 */
function getSetting($key, $setting_type, $default = '')
{
    
    
    $value = App\Settings::getSetting($key, $setting_type );
    
    if ( 'invalid_setting' === $value ) {
      $value = $default;
    }
    return $value;
}

 /**
 * This method fetches the specified key in the type of setting
 * @param  [type] $key          [description]
 * @param  [type] $setting_type [description]
 * @return [type]               [description]
 */
function getSettings( $group )
{
    $value = App\Settings::getSettings( $group );
    
    if ( 'invalid_setting' === $value ) {
      $value = '';
    }
    return $value;
}

 /**
 * This method fetches the specified key in the type of setting
 * @param  [type] $key          [description]
 * @param  [type] $setting_type [description]
 * @return [type]               [description]
 */
function getSettingTheme($key, $setting_type, $default = '')
{
    $value = Modules\SiteThemes\Entities\SiteTheme::getSetting($key, $setting_type );
    
    if ( 'invalid_setting' === $value ) {
      $value = $default;
    }
    return $value;
}

function digiTodayDate( $time = false ) {
  $format = digiDateFormat();
  if ( $time ) {
    $format .= ' h:i A';
  }
  return date( $format , time() );
}

function digiTodayDateDB( $time = false ) {
  $format = 'Y-m-d';
  if ( $time ) {
    $format .= ' H:i:s';
  }
  return date( $format , time() );
}

function digiTodayDateAdd( $days = 0 ) {
  if ( $days == 0 ) {
    return date( digiDateFormat(), time() );
  } else {
    return date( digiDateFormat(), strtotime("+$days days", time()) );
  }
}

function digiDate( $date, $time = false ) {
  $format = digiDateFormat();
  if ( $time ) {
    $format .= ' h:i A';
  }
  return date( $format, strtotime( $date ) );
}



function digiDateTimestamp( $timestamp, $time = false ) {
  $format = digiDateFormat();
  if ( $time ) {
    $format .= ' h:i A';
  }
  return date( $format, $timestamp );
}

function humanFilesize($size, $precision = 2) {
    $units = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $step = 1024;
    $i = 0;

    while (($size / $step) > 0.9) {
        $size = $size / $step;
        $i++;
    }
    
    return round($size, $precision).$units[$i];
}

function digiDateFormat() {
  return getDateFormatNew(true);
}

function getProducts( $status = 'Active', $conditions = [] ) {
  $query = \App\Product::query();
  $query->select([
      'products.id',
      'products.name',
      'products.excerpt',
      'products.description',
      'products.product_code',
      'products.actual_price',
      'products.sale_price',
      'products.stock_quantity',
      'products.hsn_sac_code',
      'products.alert_quantity',  
      'products.tax_id',
      'products.discount_id',
      'products.measurement_unit',
      'products.thumbnail',

      'products.product_size',
      'products.product_weight',
      'products.product_status',
    'products.prices',
  ]);

  if ( 'all' !== $status ) {
    $query->where( 'product_status', '=', $status );
  }

  if ( ! empty( $conditions ) ) {
    if ( ! empty( $conditions['quantity']['condition'] ) && ! empty( $conditions['quantity']['value'] ) ) {
      if ( '>' === $conditions['quantity']['condition']  ) {
        $query->where( 'stock_quantity', '>', $conditions['quantity']['value'] );
      }
    }
    if ( ! empty( $conditions['currency']['condition'] ) && ! empty( $conditions['currency']['value'] ) ) {
      if ( 'like' === $conditions['currency']['condition']  ) {
        $query->where( 'prices_available', 'like', $conditions['currency']['value'] );
      }
    }
  }

  $records = array();
  foreach ($query->get() as $record ) {
      $tax = $record->tax;
      if ( $tax ) {
          $record->tax_rate = $tax->rate;
          $record->tax_value = $tax->rate;
          $record->rate_type = $tax->rate_type;
          if ( $tax->rate > 0 && 'percent' === $tax->rate_type ) {
              $record->tax_value = ($record->sale_price * $tax->rate) / 100;
          }
      } else {
          $record->tax_rate = 0;
          $record->tax_value = 0;
          $record->rate_type = 'percent';
      }

      $discount = $record->discount;
      if ( $discount ) {
          $record->discount_rate = $discount->discount;
          $record->discount_value = $record->discount;
          $record->discount_type = $discount->discount_type;
          if ( $discount->discount > 0 && 'percent' === $discount->discount_type ) {
              $record->discount_value = ($record->sale_price * $discount->discount) / 100;
          }
      } else {
          $record->discount_rate = 0;
          $record->discount_value = 0;
          $record->discount_type = 'percent';
      }
      
      $records[] = $record;
  }
  return $records;
}

function getTasks( $project_id, $status = 'Active' ) {
  $query = \App\ProjectTask::query();
  $query->select([
      'project_tasks.id',
      'project_tasks.name',
      'project_tasks.description',
      'project_tasks.startdate',
      'project_tasks.duedate',
      'project_tasks.datefinished',
      'project_tasks.billable',
      'project_tasks.billed',
      'project_tasks.hourly_rate',
  ]);

  $query->where( 'billable', '=', 'yes' )->where('billed', '=', 'no')->where('project_id', '=', $project_id);

  $records = array();
  foreach ($query->get() as $record ) {
      $record->tax_rate = 0;
      $record->tax_value = 0;
      $record->rate_type = 'percent';

      $record->discount_rate = 0;
      $record->discount_value = 0;
      $record->discount_type = 'percent';
      
      $records[] = $record;
  }
  return $records;
}

function getProductDetails( $request ) {
  $products_details = array(
        'product_name' => $request->product_name,
        'product_qty' => $request->product_qty,
        'product_price' => $request->product_price,
        'product_amount' => $request->product_amount, // Product Quantity * Product Price

        'product_tax' => $request->product_tax, // Rate
        'tax_type' => $request->tax_type,
        'tax_value' => $request->tax_value,

        'product_discount' => $request->product_discount, // Rate
        'discount_type' => $request->discount_type,
        'discount_value' => $request->discount_value,

        'product_subtotal' => $request->product_subtotal, //( price * quantity ) + tax_value - discount_value;
        'pid' => $request->pid, // Row ID
        'unit' => $request->unit,
        'hsn' => $request->hsn,
        'alert' => $request->alert,
        'stock_quantity' => $request->stock_quantity,
        'product_ids' => $request->product_ids,
        'product_description' => $request->product_description,

        'total_tax' => $request->total_tax,
        'total_discount' => $request->total_discount,
        'products_amount' => $request->products_amount, // Amount without Tax and Discount
        'sub_total' => $request->sub_total, // products_amount +   total_tax          
        'grand_total' => $request->grand_total, // sub_total- total_discount
    );

    // Let us calculate tax, discount, grandtotal at server side. If user cahgned through JS. we can prevent here!!
    $products = ( Object ) $products_details;
    $product_names = $products->product_name;

    if ( ! empty( $product_names ) ) {
        
        $product_qtys = $products->product_qty;
        $product_prices = $products->product_price;
        $product_amounts = $products->product_amount;

        $product_taxs = $products->product_tax;
        $tax_types = $products->tax_type;
        $tax_values = $products->tax_value;

        $product_discounts = $products->product_discount;
        $discount_types = $products->discount_type;
        $discount_values = $products->discount_value;

        $product_subtotals = $products->product_subtotal;
        $pids = $products->pid;
        $units = $products->unit;
        $hsns = $products->hsn;
        $alerts = $products->alert;
        $stock_quantitys = $products->stock_quantity;
        $product_ids = $products->product_ids;
        $product_descriptions = $products->product_description;

        $total_tax = 0;
        $total_discount = 0;
        $total_products_amount = 0;
        $sub_total = 0;
        $grand_total = 0;
        $products_sync = [];
        $products_sync_tasks = []; // While we are creating/updating invoice of a project, it contains projects tasks. We need to reat them separately.
        $products_sync_expenses = []; // While we are creating/updating invoice of a project, it may contains projects expenses. We need to reat them separately.

        for( $i = 0; $i < count( $product_names ); $i++ ) {
            $product_name = ! empty( $product_names[ $i ] ) ? $product_names[ $i ] : '';
            $product_qty = ! empty( $product_qtys[ $i ] ) ? $product_qtys[ $i ] : '1';
            $product_price = ! empty( $product_prices[ $i ] ) ? $product_prices[ $i ] : '0';
            $product_amount = $product_qty * $product_price;
            $product_amounts[ $i ] = $product_amount; // Changed here.
            $total_products_amount += $product_amount;

            $product_tax = ! empty( $product_taxs[ $i ] ) ? $product_taxs[ $i ] : '0'; // Rate.
            $tax_type = ! empty( $tax_types[ $i ] ) ? $tax_types[ $i ] : 'percent';
            $tax_value = ! empty( $tax_values[ $i ] ) ? $tax_values[ $i ] : '0';
            if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                $tax_value = ( $product_amount * $product_tax) / 100;
            } else {
                $tax_value = $product_tax;
            }
            $tax_values[ $i ] = $tax_value; // Changed Here.
            $total_tax += $tax_value;

            $product_discount = ! empty( $product_discounts[ $i ] ) ? $product_discounts[ $i ] : '0'; // Rate.
            $discount_type = ! empty( $discount_types[ $i ] ) ? $discount_types[ $i ] : 'percent';
            $discount_value = ! empty( $discount_values[ $i ] ) ? $discount_values[ $i ] : '0';
            if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                $discount_value = ( $product_amount * $product_discount) / 100;
            } else {
                $discount_value = $product_discount;
            }
            $discount_values[ $i ] = $product_discount; // Changed Here.
            $total_discount += $discount_value;


            $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
            $product_subtotals[ $i ] = $amount;
            $grand_total += $amount;
            $sub_total +=  $amount + $discount_value;

            $pid = ! empty( $pids[ $i ] ) ? $pids[ $i ] : '';
            $unit = ! empty( $units[ $i ] ) ? $units[ $i ] : '';
            $hsn = ! empty( $hsns[ $i ] ) ? $hsns[ $i ] : '';
            $alert = ! empty( $alerts[ $i ] ) ? $alerts[ $i ] : '';
            $stock_quantity = ! empty( $stock_quantitys[ $i ] ) ? $stock_quantitys[ $i ] : '';
            $product_id = ! empty( $product_ids[ $i ] ) ? $product_ids[ $i ] : '';
            $product_description = ! empty( $product_descriptions[ $i ] ) ? $product_descriptions[ $i ] : '';

            $sync_product = [
              'product_id' => $product_id,
              'product_name' => $product_name,
              'product_qty' => $product_qty,
              'product_price' => $product_price,
              
              'product_tax' => $product_tax, // Rate
              'tax_type' => $tax_type,
              'tax_value' => $tax_value,

              'product_discount' => $product_discount, // Rate
              'discount_type' => $discount_type,
              'discount_value' => $discount_value,

              'product_subtotal' => $amount,
              'product_amount' => $amount,
              'pid' => $pid,
              'unit' => $unit,
              'hsn' => $hsn,
              'alert' => $alert,
              'stock_quantity' => $stock_quantity,              
              'product_description' => $product_description,
            ];

            
            $type = $id = '';
            $parts = explode( '_', $product_name );
            if ( ! empty( $parts[0] ) ) {
                $id = $parts[0];
            }
            if ( ! empty( $parts[1] ) ) {
                $type = $parts[1];
            }
            if ( 'task' === $type ) {
              unset($sync_product['product_id']);
              $sync_product['task_id'] = $id;
              $products_sync_tasks[] = $sync_product;
            } elseif( 'expense' === $type ) {
              unset($sync_product['product_id']);
              $sync_product['expense_id'] = $id;
              $products_sync_expenses[] = $sync_product;
            } else {
              $products_sync[] = $sync_product;
            }
        }

        $products_details['tax_value'] = $tax_values;
        $products_details['discount_value'] = $discount_values;
        $products_details['product_amount'] = $product_amounts;
        $products_details['product_subtotal'] = $product_subtotals;

        $products_details['total_discount'] = $total_discount;
        $products_details['total_tax'] = $total_tax;
        $products_details['products_amount'] = $total_products_amount;
        $products_details['sub_total'] = $sub_total;
        $products_details['grand_total'] = $grand_total;

        $products_details['products_sync'] = $products_sync;
        $products_details['products_sync_tasks'] = $products_sync_tasks;
        $products_details['products_sync_expenses'] = $products_sync_expenses;
    }

    return $products_details;
}

/**
 * This is a common method to send emails based on the requirement
 * The template is the key for template which is available in db
 * The data part contains the key=>value pairs 
 * That would be replaced in the extracted content from db
 * @param  [type] $template [description]
 * @param  [type] $data     [description]
 * @return [type]           [description]
 */
 function sendEmail($template, $data)
 {
    return (new \Modules\Templates\Entities\Template())->sendEmail($template, $data);
 }

 /**
 * Returns the template html code by forming header, body and footer
 * @param  [type] $template [description]
 * @return [type]           [description]
 */
function getTemplate($template, $data)
{
    
    $header = \Modules\Templates\Entities\Template::where('title', '=', 'header')->first();
    $footer = \Modules\Templates\Entities\Template::where('title', '=', 'footer')->first();
    if ( is_string( $template ) ) {
      $template = \Modules\Templates\Entities\Template::where('key', '=', $template)->first();
    }

    $content = $template->content;
    if ( isset( $data['content'] ) ) {
        $content = $data['content'];
    }
       
    $view = \View::make('admin.invoices.mail.template', [
                                            'header' => $header->content, 
                                            'footer' => $footer->content,
                                            'body'  => $content, 
                                            ]);

    return $view->render();
}

/**
 * Prepares the view from string passed along with data
 * @param  [type] $__php  [description]
 * @param  [type] $__data [description]
 * @return [type]         [description]
 */
function render($__php, $__data)
{
    $obLevel = ob_get_level();
    ob_start();
    extract($__data, EXTR_SKIP);
    try {
        eval('?' . '>' . $__php);
    } catch (Exception $e) {
        while (ob_get_level() > $obLevel) ob_end_clean();
        throw $e;
    } catch (Throwable $e) {
        while (ob_get_level() > $obLevel) ob_end_clean();
        throw new FatalThrowableError($e);
    }
    return ob_get_clean();
}

 /**
 * This is a common method to send emails based on the requirement
 * The template is the key for template which is available in db
 * The data part contains the key=>value pairs 
 * That would be replaced in the extracted content from db
 * @param  [type] $template [description]
 * @param  [type] $data     [description]
 * @return [type]           [description]
 */
 function sendSms($template, $data)
 {
    return (new \Modules\Smstemplates\Entities\Smstemplate())->sendSms($template, $data);
 }

 /**
 * This method returns the path of the user image based on the type
 * It verifies wether the image is exists or not, 
 * if not available it returns the default image based on type
 * @param  string $image [Image name present in DB]
 * @param  string $type  [Type of the image, the type may be thumb or profile, 
 *                       by default it is thumb]
 * @return [string]      [returns the full qualified path of the image]
 */
function getCompanyLogo($image = '', $type = 'thumb')
{
    $obj = app('App\ImageSettings');
    $path = '';
    
    if($image=='') {
        if($type=='logo')
            return PREFIX.$obj->getDefaultCompanyLogoPath();
        return PREFIX.$obj->getDefaultCompanyLogoThumbnailpath();
    }
  

    if($type == 'logo')
        $path = $obj->getCompanyLogoPath();
    else
        $path = $obj->getCompanyLogoThumbnailpath();
    $imageFile = $path.$image;

    if (File::exists($imageFile)) {
        return PREFIX.$imageFile;
    }

    if($type=='logo')
        return PREFIX.$obj->getDefaultCompanyLogoPath();
    return PREFIX.$obj->getDefaultCompanyLogoThumbnailpath();

}

function getSettingsPath()
{
  $imageObject = new \App\ImageSettings();          
  $destinationPath      = public_path() . $imageObject->getSettingsImagePath();
  return $destinationPath;
}
function GetIP()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
    {
        if (array_key_exists($key, $_SERVER) === true)
        {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
            {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                {
                    return $ip;
                }
            }
        }
    }
}

function getController( $key = '' ) {
  $action = app('request')->route();
  if ( $action ) {
    $action = $action->getAction();
  }

  $controller = class_basename($action['controller']);

  $parts = explode('@', $controller);

  $controller = $parts[0];
  if ( ! empty( $parts[1] ) ) {
    $action = $parts[1];
  }


  $result = array(
    'controller' => $controller,
    'action' => $action,
  );
  if ( ! empty( $key ) ) {
    if ( ! empty( $result[ $key ] ) ) {
      $result = $result[ $key ];
    }
  }
  return $result;
}

function isValidTimeStamp($timestamp)
{
    return ((string) (int) $timestamp === $timestamp) 
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
}

function isAdmin( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_ADMIN ) {
      return true;
    } elseif ( $role->slug === ROLE_EXECUTIVE ) { // We are considering executive as admin
      return true;
    }
  }
  return false;
}

function isLead( $id = '', $type = 'contact' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->id === LEADS_TYPE ) {
      return true;
    }
  }
  return false;
}

//EXECUTIVE ROLE

function isExecutive( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_EXECUTIVE ) {
      return true;
    }
  }
  return false;
}

//END EXECUTIVE


function isCustomer( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_CUSTOMER ) {
      return true;
    }
  }
  return false;
}

function isBusinessManager( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_BUSINESS_MANAGER ) {
      return true;
    }
  }
  return false;
}

function isSupplier( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_SUPPLIER ) {
      return true;
    }
  }
  return false;
}

function isSalesManager( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_SALES_MANAGER ) {
      return true;
    }
  }
  return false;
}

function isSalesPerson( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_SALES_PERSON ) {
      return true;
    }
  }
  return false;
}

function isProjectManager( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_PROJECT_MANAGER ) {
      return true;
    }
  }
  return false;
}

function isStockManager( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_STOCK_MANAGER ) {
      return true;
    }
  }
  return false;
}

function isClient( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_CLIENT ) {
      return true;
    }
  }
  return false;
}

function isEmployee( $id = '', $type = 'user' ) {
  $roles = Auth::user()->role()->get();
  if ( ! empty( $id ) ) {
    if ( 'contact' === $type ) {
      $roles = \App\Contact::find( $id )->contact_type()->get();
    } else {
      $roles = \App\User::find( $id )->role()->get();
    }
  }
  foreach( $roles as $role ) {
    if ( $role->slug === ROLE_EMPLOYEE ) {
      return true;
    }
  }
  return false;
}

function getRoleIdSlug( $slug ) {
  $id = 0;
  $role = \App\Role::where('slug', '=', $slug)->first();
  if ( $role ) {
    $id = $role->id;
  }
  return $id;
}

function getContactId() {
  return Auth::id();
}

function hasTransactions() {
  $invocie_payments = \Modules\InvoicePayments\Entities\InvoicePayment::count();
}


function getContactInfo( $user_id = '', $key = '' ) {
  
  if ( ! empty( $user_id ) ) {
    $contact = \App\Contact::where( 'id', '=', $user_id)->first();
  } else {
    $contact = \App\Contact::where( 'id', '=', Auth::id())->first();
  }

  if ( isset( $contact->$key ) ) {
    return $contact->$key;
  } else {
    return null;
  }
}

/**
 * CartOrdersProduct : $cartorderproducts
 */
function productsAmountDetails( $cartorderproducts, $key = '' ) {
  $total = $sub_total = $discount_total = $tax_total = $rowid = 0;
  $products = array();
  
  $currency_code = getDefaultCurrency( 'code' );

  foreach( $cartorderproducts as $product) {
    $record = $product->product; // Related product details.

    $price = $record->sale_price;
    $prices = ! empty($record->prices) ? json_decode( $record->prices, true ) : array();
    if ( isCustomer() && ! empty( $prices['sale'][ $currency_code ] ) ) {
        $price = $prices['sale'][ $currency_code ]; // If customer is on orders page we need to display prices in his own currency.
    }
    $quantity = $product->quantity;
    $amount = $product_total = $quantity * $price;
    $sub_total += $product_total; // Amount with out tax and discount.


    $tax_value = $tax_rate = 0;
    $rate_type = 'percent';
    $tax = $record->tax;
    if ( $tax ) {
      $tax_rate = $tax->rate;
      $tax_value = $tax_rate * $quantity;
      $rate_type = $tax->rate_type;
      if ( $tax_rate > 0 && 'percent' === $rate_type ) {
        $tax_value = ($amount * $tax_rate) / 100;
      }
    }
    $tax_total += $tax_value;

    $discount_value = $discount_rate = 0;
    $discount_type = 'percent';
    $discount = $record->discount;
    if ( $discount ) {
      $discount_rate = $discount->discount;
      $discount_value = $discount_rate * $quantity;
      $discount_type = $discount->discount_type;
      if ( $discount_rate > 0 && 'percent' === $discount_type ) {
      $discount_value = ($amount * $discount_rate) / 100;
      }
    }
    $discount_total += $discount_value;

    $amount = $amount - $discount_value + $tax_value;
    $total += $amount;

    $products['price'] = $price;
    $products['quantity'] = $quantity;
    $products['product_total'] = $product_total; // $quantity * $price;

    $products['tax_rate'] = $tax_rate;
    $products['rate_type'] = $rate_type;
    $products['tax_value'] = $tax_value;

    $products['discount_rate'] = $discount_rate;
    $products['discount_type'] = $discount_type;
    $products['discount_value'] = $discount_value;

    $products['product_id'] = $record->id;
    //$products['product_id'] = $record->id;
  }

  $details = array(
    'total' => $total, // Payable amount
    'sub_total' => $sub_total,
    'discount_total' => $discount_total,
    'tax_total' => $tax_total,
    'products' => $products,
  );
  if ( isset( $details[ $key ] ) ) {
    return $details[ $key ];
  } else {
    return $details;
  }
}

function stripeCurrencies() {
  return [
    'usd', 'aed', 'afn', 'all', 'amd', 'ang', 'aoa', 'ars', 'aud', 'awg', 'azn', 'bam', 'bbd', 'bdt', 'bgn', 'bif', 'bmd', 'bnd', 'bob', 'brl', 'bsd',
    'bwp', 'bzd', 'cad', 'cdf', 'chf', 'clp', 'cny', 'cop', 'crc', 'cve', 'czk', 'djf', 'dkk', 'dop', 'dzd', 'egp', 'etb', 'eur', 'fjd', 'fkp', 'gbp',
    'gel', 'gip', 'gmd', 'gnf', 'gtq', 'gyd', 'hkd', 'hnl', 'hrk', 'htg', 'huf', 'idr', 'ils', 'inr', 'isk', 'jmd', 'jpy', 'kes', 'kgs', 'khr', 'kmf', 
    'krw', 'kyd', 'kzt', 'lak', 'lbp', 'lkr', 'lrd', 'lsl', 'mad', 'mdl', 'mga', 'mkd', 'mmk', 'mnt', 'mop', 'mro', 'mur', 'mvr', 'mwk', 'mxn', 'myr', 
    'mzn', 'nad', 'ngn', 'nio', 'nok', 'npr', 'nzd', 'pab', 'pen', 'pgk', 'php', 'pkr', 'pln', 'pyg', 'qar', 'ron', 'rsd', 'rub', 'rwf', 'sar', 'sbd', 
    'scr', 'sek', 'sgd', 'shp', 'sll', 'sos', 'srd', 'std', 'szl', 'thb', 'tjs', 'top', 'try', 'ttd', 'twd', 'tzs', 'uah', 'ugx', 'uyu', 'uzs', 'vnd', 
    'vuv', 'wst', 'xaf', 'xcd', 'xof', 'xpf', 'yer', 'zar', 'zmw', 'eek', 'lvl', 'svc', 'vef',
  ];
}

function paypalCurrencies() {
  return ['AUD', 
  'BRL', // This currency is supported as a payment currency and a currency balance for in-country PayPal accounts only.
  'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 
  'HUF', // This currency does not support decimals. If you pass a decimal amount, an error occurs.
  //'INR', // This currency is supported as a payment currency and a currency balance for in-country PayPal India accounts only.
  'ILS', 
  'JPY', // This currency does not support decimals. If you pass a decimal amount, an error occurs.
  'MYR', // This currency is supported as a payment currency and a currency balance for in-country PayPal accounts only.
  'MXN', 
  'TWD', // This currency does not support decimals. If you pass a decimal amount, an error occurs.
  'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD'];
}


/**
* Common method to send user restriction message for invalid attempt 
* @return [type] [description]
*/
function prepareBlockUserMessage( $status = 'danger', $operation = 'create', $message = 'not-allowed')
{
  flashMessage($status, $operation, $message);
  return back();
}

function isDemo()
{
  if ( config('app.demo') ) {
    return true;
  } else {
    return false;
  }
}

function isEnable( $variable )
{
  if ( config('app.' . $variable) ) {
    return true;
  } else {
    return false;
  }
}




/////////////////////////////Language helper start///////////////////////////////
function translate($sl, $tl, $q)
{
    
    $apiKey = getSetting('api_key', 'translations');
    if ( empty( $apiKey ) ) {
      $apiKey = 'AIzaSyDoKNAWR3TU1j7KlfLmY8XfTHiwCP6jiVc';
    }
  
    $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($q) . '&source='.$sl.'&target=' . $tl;

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);     //We want the result to be saved into variable, not printed out
    $response = curl_exec($handle);                         
    curl_close($handle);

    $responseDecoded = json_decode($response, true);

    $text = ! empty( $responseDecoded['data']['translations'][0]['translatedText'] ) ? $responseDecoded['data']['translations'][0]['translatedText'] : $q;
     
    return $text;
}
/////////////////////////////Language helper end///////////////////////////////


function getRoleId( $role_name ) {
  return \App\Role::where('slug', '=', $role_name)->first()->id;
}

function getLanguageId( $code ) {
  return \App\Language::where('code', '=', $code)->first()->id;
}


function getCountryname( $country_id ) {
  $name = \App\Country::find( $country_id );
  if ( $name ) {
    $name = $name->title;
  } else {
    $name = '';
  }
  return $name;
}

function yesnooptions( $value_caps = false )
{
  if ( $value_caps ) {
    return array(
      'Yes'  => trans('custom.common.yes'),
      'No'    => trans('custom.common.no'),
    );
  } else {
    return array(
      'yes'  => trans('custom.common.yes'),
      'no'    => trans('custom.common.no'),
    );
  }
}

if (! function_exists( 'digi_get_help' ) ) {
  /**
   * Returns the help text as tooltip
   *
   * @since 1.0
   * @return string
   */
  function digi_get_help( $help = 'This is help text', $icon = 'fa fa-question-circle fa-lg', $class = '' ) {
    $text = sprintf( '&nbsp;<span class="st_tooltip '.$class.'" title="%s" data-toggle="tooltip"><span class="' . $icon . '"></span></span>', $help );
    return $text;
  }
}

if ( ! function_exists( 'getOption' ) ) {
  function getOption( $name, $default = '' ) {
    $value = $default;
    $option = \App\Option::where('name', $name)->first();
    if ( $option ) {
      $value = $option->value;
    }
    return $value;
  }
}

if ( ! function_exists( 'updateOption' ) ) {
  function updateOption( $name, $value = '', $default = '' ) {
    $value = $default;
    $option = \App\Option::where('name', '=', $name)->first();
    if ( $option ) {
      $option->value = $value;
      $option->save();
    } else {
      $option = \App\Option::create(array('name' => $name, 'value' => $value));
    }
    return $value;
  }
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Language Helper
 * @param  string|null  $phrase
 * @return string
 */
function getPhrase($key = null)
{
  
    $phrase = app('App\Language');

    if (func_num_args() == 0) {
        return '';
    }

    return  $phrase::getPhrase($key); 
}


/**
 * This method is used to return the default validation messages
 * @param  string $key [description]
 * @return [type]      [description]
 */
function getValidationMessage($key='required')
{
    $message = '<p ng-message="required">'.getPhrase('this_field_is_required').'</p>';    
    
    if($key === 'required')
        return $message;

        switch($key)
        {
          case 'minlength' : $message = '<p ng-message="minlength">'
                                        .getPhrase('the_text_is_too_short')
                                        .'</p>';
                                        break;
          case 'maxlength' : $message = '<p ng-message="maxlength">'
                                        .getPhrase('the_text_is_too_long')
                                        .'</p>';
                                        break;
          case 'pattern' : $message   = '<p ng-message="pattern">'
                                        .getPhrase('invalid_input')
                                        .'</p>';
                                        break;
            case 'image' : $message   = '<p ng-message="validImage">'
                                        .getPhrase('please_upload_valid_image_type')
                                        .'</p>';
                                        break;
          case 'email' : $message   = '<p ng-message="email">'
                                        .getPhrase('please_enter_valid_email')
                                        .'</p>';
                                        break;
                                       
          case 'number' : $message   = '<p ng-message="number">'
                                        .getPhrase('please_enter_valid_number')
                                        .'</p>';
                                        break;

          case 'confirmPassword' : $message   = '<p ng-message="compareTo">'
                                        .getPhrase('password_and_confirm_password_does_not_match')
                                        .'</p>';
                                        break;
           case 'password' : $message   = '<p ng-message="minlength">'
                                        .getPhrase('the_password_is_too_short')
                                        .'</p>';
                                        break;
           case 'phone' : $message   = '<p ng-message="minlength">'
                                        .getPhrase('please_enter_valid_phone_number')
                                        .'</p>';
                                        break;
        }
    return $message;
}


/**
 * This method identifies if the url contains the specific string
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
 function urlHasString($str)
 {
    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
     if (strpos($url, $str)) 
        return TRUE;
    return FALSE;
                
 }


 /**
 * This method returns the user based on the sent userId, 
 * If no userId is passed returns the current logged in user
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
 function getUserRecord($user_id = 0)
 {
    if($user_id)
     return (new App\User())->where('id','=',$user_id)->first();
    return Auth::user();
 }


  /**
  * Returns the appropriate layout based on the user logged in
  * @return [type] [description]
  */
 function getLayout()
 {
    $layout = 'layouts.home';
    if (checkRole(getUserGrade(1)))
        $layout  = 'layouts.app';
    elseif (checkRole(['seller']))
        $layout  = 'layouts.seller';


    return $layout;
 }


/**
 * This method returns the role of the currently logged in user
 * @return [type] [description]
 */
 function getRole($user_id = 0)
 {
  
    if($user_id)
        return getUserRecord($user_id)->roles()->first()->name;
    else {
        $roles = Auth::user()->roles()->first();
      if ($roles)
        return $roles->name;
    }
    return redirect('logout');
 }

 /**
 * Returns the user record with the matching slug.
 * If slug is empty, it will return the currently logged in user
 * @param  string $slug [description]
 * @return [type]       [description]
 */
function getUserWithSlug($slug='')
{
    if($slug)
     return App\User::where('slug', $slug)->get()->first();
    return Auth::user();
}


/**
 * Returns the predefined Regular Expressions for validation purpose
 * @param  string $key [description]
 * @return [type]      [description]
 */
function getRegexPattern($key='name')
{
    $phone_regx = getSetting('phone_number_expression', 'site_settings');
    $pattern = array(
                    'name' =>  '/(^[A-Za-z0-9-., ]+$)+/', 
                    'email' => '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',
                    'phone'=>$phone_regx,
                    'price'=> '/^((([0-9]*)[\.]([0-9]{2}))|([0-9]*))$/',
                    'numbers'=> '/^([0-9]*)$/'
                    );
    return $pattern[$key];
}


/**
 * This method will send the random color to use in graph
 * The random color generation is based on the number parameter 
 * As the border and bgcolor need to be same, 
 * We are maintainig number parameter to send the same value for bgcolor and background color
 * @param  string  $type   [description]
 * @param  integer $number [description]
 * @return [type]          [description]
 */
 function getColor($type = 'background',$number = 777) {

    $hash = md5('color'.$number); // modify 'color' to get a different palette
    $color = array(
        hexdec(substr($hash, 0, 2)), // r
        hexdec(substr($hash, 2, 2)), // g
        hexdec(substr($hash, 4, 2))); //b
    if($type=='border')
    return 'rgba('.$color[0].','.$color[1].','.$color[2].',1)';
    return 'rgba('.$color[0].','.$color[1].','.$color[2].',0.2)';
}


/**
 * This method returns the path of the user image based on the type
 * It verifies wether the image is exists or not, 
 * if not available it returns the default image based on type
 * @param  string $image [Image name present in DB]
 * @param  string $type  [Type of the image, the type may be thumb or profile, 
 *                       by default it is thumb]
 * @return [string]      [returns the full qualified path of the image]
 */
function getProfilePath($image = '', $type = 'thumb')
{
    $obj = app('App\ImageSettings');
    $path = '';
    
    if($image=='') {
        if($type=='profile')
            return PREFIX.$obj->getDefaultProfilePicPath();
        return PREFIX.$obj->getDefaultprofilePicsThumbnailpath();
    }
  

    if($type == 'profile')
        $path = $obj->getProfilePicsPath();
    else
        $path = $obj->getProfilePicsThumbnailpath();
    $imageFile = $path.$image;

    if (File::exists($imageFile)) {
        return PREFIX.$imageFile;
    }

    if($type=='profile')
        return PREFIX.$obj->getDefaultProfilePicPath();
    return PREFIX.$obj->getDefaultprofilePicsThumbnailpath();

}





/**
 * Active class Helper
 * @param  string|null  $phrase
 * @return string
 */
function isActive($active_class = '', $value = '')
{

    $value = isset($active_class) ? ($active_class == $value) ? 'active' : '' : '';
    if($value)
        return "class = ".$value;
    return $value; 
}



/**
 * Bidder Active class Helper
 * @param  string|null  $phrase
 * @return string
 */
function bidderActive($active_class = '', $value = '')
{
 
    $value = isset($active_class) ? ($active_class == $value) ? 'active isactive' : '' : '';
    if($value)
        return "class = ".$value;
    return $value; 
}




 /**
  * Common method to send user restriction message for invalid attempt 
  * @return [type] [description]
  */
 function pageNotFound()
 {
    flash('Ooops..!', 'page_not_found', 'error');
     return '';
 }
 
 

 function getArrayFromJson($jsonData)
{
    $result = array();
    if($jsonData)
    {
        foreach(json_decode($jsonData) as $key=>$value)
            $result[$key] = $value;
    }
    return $result;
}


function prepareArrayFromString($string='', $delimeter = '|')
{
  
    return explode($delimeter, $string);
}

 function isEligible($slug)
 {
     if(!checkRole(getUserGrade(1)))
     {
        if(!validateUser($slug)) 
        {
          prepareBlockUserMessage();
          return FALSE;
        }
     }
     return TRUE;
 }

function validateUser($slug)
 {

    $user = \Auth::user();

    if (!$user)
      return redirect(URL_USERS_LOGIN);
    else if($slug == $user->slug)
        return TRUE;

    return FALSE;
 }



/**
 * Returns the random hash unique code
 * @return [type] [description]
 */
function getHashCode()
{
  return bin2hex(openssl_random_pseudo_bytes(20));
}


/**
 * This method returns the path of the logo of the bank
 * It verifies whether the image is exists or not, 
 * if not available it returns the default image based on type
 * @param  string $image [Image name present in DB]
 * @param  string $type  [Type of the image, the type may be thumb or profile, 
 *                       by default it is thumb]
 * @return [string]      [returns the full qualified path of the image]
 */
function getBankLogosPath($image = '', $type = 'thumb')
{
    $obj = app('App\ImageSettings');
    $path = '';
    
    if($image=='') {
        if($type=='bank')
            return PREFIX.$obj->getDefaultProfilePicPath();
        return PREFIX.$obj->getDefaultprofilePicsThumbnailpath();
    }
  

    if($type == 'bank')
        $path = $obj->getBankLogosPath();
    else
        $path = $obj->getBankLogosThumbnailpath();
    $imageFile = $path.$image;

    if (File::exists($imageFile)) {
        return PREFIX.$imageFile;
    }

    if($type=='bank')
        return PREFIX.$obj->getDefaultProfilePicPath();
    return PREFIX.$obj->getDefaultprofilePicsThumbnailpath();

}



/**
 * This method returns the path of the property image
 * It verifies whether the image is exists or not, 
 * if not available it returns the default image based on type
 * @param  string $image [Image name present in DB]
 * @param  string $type  [Type of the image, the type may be thumb or profile, 
 *                       by default it is thumb]
 * @return [string]      [returns the full qualified path of the image]
 */
function getAuctionImage($image = '', $type = 'thumb')
{
    $obj = app('App\ImageSettings');
    $path = '';
    
    if($image=='') {
        if($type=='auction')
            return PREFIX.$obj->getDefaultAuctionImagePath();
        return PREFIX.$obj->getDefaultAuctionImageThumbnailpath();
    }
  

    if($type == 'auction')
        $path = $obj->getAuctionImagePath();
    else
        $path = $obj->getAuctionImageThumbnailpath();
    $imageFile = $path.$image;

    if (File::exists($imageFile)) {
        return PREFIX.$imageFile;
    }

    if($type=='auction')
        return PREFIX.$obj->getDefaultAuctionImagePath();
    return PREFIX.$obj->getDefaultAuctionImageThumbnailpath();

}




function activeinactive()
{
   return array('Active'   => getPhrase('active'),
                'Inactive' => getPhrase('inactive'));
   
}


function auctionstatusoptions()
{
   return array('new'   => getPhrase('new'),
                'open'  => getPhrase('open'),
                'suspended' => getPhrase('suspended'),
                'closed'    => getPhrase('closed')
              );
   
}


function adminstatusoptions()
{
   return array('pending'   => getPhrase('pending'),
                'approved'  => getPhrase('approved'),
                'rejected'  => getPhrase('rejected'));
}

function templatetypes()
{
   return array('Content'   => getPhrase('content'),
                'Header' => getPhrase('header'),
                'Footer'=> getPhrase('footer')
              );
   
}


function pricebidoptions()
{
  return array('applicable'  => getPhrase('applicable'),
            'not_applicable' => getPhrase('not_applicable'));
}



function bidderstatusoptions()
{
  return array('pending' => getPhrase('pending'),
              'approved' => getPhrase('approved'),
              'rejected' => getPhrase('rejected')
            );
  
}


if ( ! function_exists('clean_text'))
{
  function clean_text($string) 
  {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.
  }
}

if ( ! function_exists('get_text'))
{
  function get_text($string) 
  {
    $string = str_replace('_', ' ', $string); // Replaces hyphen with space.
    return ucwords($string);
  }
}


 function setDescriptionLimit($description)
{
    $string = strip_tags($description);

    $charlength = 50;

    if (strlen($string) > $charlength ) {

        // truncate string
        $stringCut = substr($string, 0, $charlength);

        // make sure it ends in a word so assassinate doesn't become ass...
        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
    }
    return $string;
}






/**
 * Returns the max records per page
 * @return [type] [description]
 */
function getRecordsPerPage()
{
  return RECORDS_PER_PAGE;
}




/**
 * This method returns the path of the bidder signature
 * It verifies whether the image is exists or not, 
 * if not available it returns the default image based on type
 * @param  string $image [Image name present in DB]
 * @param  string $type  [Type of the image, the type may be thumb or profile, 
 *                       by default it is thumb]
 * @return [string]      [returns the full qualified path of the image]
 */
function getBidderSignature($image = '', $type = 'thumb')
{
    $obj = app('App\ImageSettings');
    $path = '';
    
    if($image=='') {
       return NULL;
    }
  
    if($type == 'signature')
        $path = $obj->getBidSignaturesPath();
    else
        $path = $obj->getBidSignaturesThumbnailpath();
    $imageFile = $path.$image;

    if (File::exists($imageFile)) {
        return PREFIX.$imageFile;
    }

    return NULL;
}


/**
 * This method returns the path of the bidder signature
 * It verifies whether the image is exists or not, 
 * if not available it returns the default image based on type
 * @param  string $image [Image name present in DB]
 * @param  string $type  [Type of the image, the type may be thumb or profile, 
 *                       by default it is thumb]
 * @return [string]      [returns the full qualified path of the image]
 */
function getBidderDocumentPath($image = '')
{
    if($image=='') {
       return UPLOADS.'public/img-example.jpg';
    }

    $imageFile = BIDDER_DOCUMENTS_UPLOADS.$image;

    if (File::exists($imageFile)) {
        return GET_BIDDER_DOCUMENTS_PATH.$image;
    }

    return UPLOADS.'public/img-example.jpg';
}

/**
 * [accountstatus description]
 * @return [type] [description]
 */
function accountstatus()
{
   return array(1   => getPhrase('approve'),
                0 => getPhrase('disapprove'));
   
}

/**
 * [getAuctionDaysLeft description]
 * @param  [type] $start_date [description]
 * @param  [type] $end_date   [description]
 * @return [type]             [description]
 */
function getAuctionDaysLeft($start_date, $end_date)
{
    if ($start_date && $end_date) {

        $startDate = DateTime::createFromFormat('Y-m-d H:i:s',$start_date);
        $endDate   = DateTime::createFromFormat('Y-m-d H:i:s',$end_date);

        $difference = $startDate->diff($endDate);

        $years   = $difference->y;
        $months  = $difference->m;
        $days    = $difference->d;
        $hours   = $difference->h;
        $minutes = $difference->i;
        $seconds = $difference->s;
        


        //years
        //months
        //days
        //hours
        //minutes
        
        if ($years>0)
          return $years>1 ? $years.' years left' : $years.' year left';
        elseif ($years<=0 && $months>0)
          return $months>1 ? $months.' months left' : $months.' month left';
        elseif ($years<=0 && $months<=0 && $days>0)
          return $days>1 ? $days.' days left' : $days.' day left';
        elseif ($years<=0 && $months<=0 && $days<=0 && $hours>0)
          return $hours>1 ? $hours.' hours left' : $hours.' hour left';
        elseif ($years<=0 && $months<=0 && $days<=0 && $hours<=0 && $minutes>0)
          return $minutes>1 ? $minutes.' mins left' : $minutes.' min '.$seconds.' sec left';

        else
          return null;
    } else 
        return null;
}


/**
 * [bidpayment description]
 * @param  [type] $ab_id [description]
 * @return [type]        [description]
 */
function bidpayment($ab_id)
{
    $result=false;

    $auctionbidder = App\AuctionBidder::getRecord($ab_id);

    if (($auctionbidder)) {
      $today = date('Y-m-d H:i:s');

      if ($auctionbidder->is_admin_sent_email=='Yes' && $auctionbidder->is_bidder_paid!='Yes' &&  strtotime($today)<=strtotime($auctionbidder->pay_end_datetime)) {
          $result = true;
      }

      //check any bidder already paid 
      $bid_payment = App\Payment::where('ab_id',$ab_id)
                                ->where('payment_status',PAYMENT_STATUS_SUCCESS)
                                ->get();
      if (count($bid_payment))
        $result = false;
    }

    return $result;
}


/**
 * [checkBillingAddress description]
 * @return [type] [description]
 */
function checkBillingAddress()
{
   $user = \Auth::user();
   if ($user->billing_country && $user->billing_state && $user->billing_city && $user->billing_name && $user->billing_email && $user->billing_phone && $user->billing_address)
      return true;
   else
      return false;
}

/**
 * [checkShippingAddress description]
 * @return [type] [description]
 */
function checkShippingAddress()
{
   $user = \Auth::user();
   if ($user->shipping_country && $user->shipping_state && $user->shipping_city && $user->shipping_name && $user->shipping_email && $user->shipping_phone && $user->shipping_address)
      return true;
   else
      return false;
}

/**
 * [getActiveTheme description]
 * @return [type] [description]
 */
function getActiveTheme( $getkey = '', $default = '' )
{
    if ( isAdmin() ) {
      $current_theme  = \Modules\SiteThemes\Entities\SiteTheme::where('is_active',1)->first();
      $theme = 'default';
      if (\Cookie::get('theme')) { 
          $theme = \Cookie::get('theme');
      }
      if ( ! empty( $theme ) ) {
        $current_theme  = \Modules\SiteThemes\Entities\SiteTheme::where('theme_title_key',$theme)->first();
      }
    } else {
      $theme_title_key = 'default';
      if ( ! empty( Auth::user()->theme ) ) {
        $theme_title_key = Auth::user()->theme;
      }
      $current_theme  = \Modules\SiteThemes\Entities\SiteTheme::where('theme_title_key', $theme_title_key)->first();
    }
    
    $settings_data = json_decode( $current_theme->settings_data );
    
    if ( ! empty( $settings_data ) ) {
      foreach ( $settings_data as $key => $value) {
        $current_theme->{$key} = $value->value;
      }
    }
    
    if ( ! empty( $getkey ) && isset( $current_theme->{$getkey} ) ) {
      $current_theme = $current_theme->{$getkey};
      if ( empty( $current_theme ) ) {
        $current_theme = $default;
      }
    }
    return $current_theme;
}

function storeLanguages() {
  foreach(config('app.languages') as $short => $title) {
    $language = array( 'language' => $title, 'code' => $short, 'is_rtl' => 'No');
    \App\Language::firstOrCreate( $language );
  }
}

function getDateFormatNew( $is_php = false ) {
  

  $format = env('DATE_FORMAT_MOMENT', config('app.date_format_moment'));

  if ( $is_php ) {
    $format = env('DATE_FORMAT', config('app.date_format'));
  }
  return $format;
}



function getDateFormat() {
  return env('DATE_FORMAT');
}

 /**
 * Sends the default Currency set for the project
 * @return [type] [description]
 */
function getCurrencyCode()
{
  return getDefaultCurrency('code') ;
}

function getStatistics( $type ) {
  $statistics = array();
  switch( $type ) {
    case 'invoice':
    break;
  }

  return $statistics;
}


function seconds_to_time_format( $seconds =0  ) {
  return \Carbon\CarbonInterval::seconds($seconds)->cascade()->forHumans();
}

function secondsToQuantity( $sec ) {
  $seconds = $sec / 3600;
  return round($seconds, 2);
}


function productshtml( $id, $type = 'invoice' ) {
  $total_tax = $sub_total = $total_discount = $grand_total = $total_paid = $total_used = 0;

  if ( 'purchaseorder' === $type ) {
    $invoice = \App\PurchaseOrder::find( $id );
    $total_paid = \App\PurchaseOrderPayment::where('purchase_order_id', $id)->where('payment_status', 'Success')->sum('amount');
  } else if( 'creditnote' === $type ) {
    $invoice = \App\CreditNote::find( $id );
    $total_paid = \App\CreditNotePayment::where('credit_note_id', $id)->where('payment_status', 'Success')->sum('amount');
    $total_used = \App\CreditNoteCredit::where('credit_note_id', $id)->sum('amount');
  } else if( 'recurringinvoice' === $type ) {
    $invoice = \Modules\RecurringInvoices\Entities\RecurringInvoice::find( $id );
    $total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', $id)->where('payment_status', 'Success')->sum('amount');
    $total_used = \App\CreditNoteCredit::where('invoice_id', $id)->sum('amount');
  } else {
    $invoice = \App\Invoice::find( $id );
    $total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', '=', $id)->where('payment_status', 'Success')->sum('amount');
    $total_used = \App\CreditNoteCredit::where('invoice_id', $id)->sum('amount');
  }

  $products = ! empty( $invoice->products ) ? json_decode( $invoice->products ) : array();
  $total_tax = ! empty( $products->total_tax ) ? $products->total_tax : 0;
  $total_discount = ! empty( $products->total_discount ) ? $products->total_discount : 0;
  $products_amount = ! empty( $products->products_amount ) ? $products->products_amount : 0;
  $sub_total = ! empty( $products->sub_total ) ? $products->sub_total : 0;
  $grand_total = ! empty( $products->grand_total ) ? $products->grand_total : 0;

  $discount_format = ! empty( $products->discount_format ) ? $products->discount_format : 'after_tax';
  $tax_format = ! empty( $products->tax_format ) ? $products->tax_format : 'after_tax';
  $cart_tax = ! empty( $products->cart_tax ) ? $products->cart_tax : 0;
  $cart_discount = ! empty( $products->cart_discount ) ? $products->cart_discount : 0;
  $amount_payable = ! empty( $products->amount_payable ) ? $products->amount_payable : 0;

  // Let us not take it from JSON format, cause it is not useful for reports later, so lets take it from separate table.
  // C: Aug 26, 2019
  $products_attached = ! empty( $invoice->id ) ? $invoice->attached_products( $id ) : [];
  if ( ! empty( $invoice->id ) && ! empty( $invoice->project_id ) ) {
    $products_attached_tasks = $invoice->attached_tasks( $id );
    $products_attached_expenses = $invoice->attached_expenses( $id );
  }

  if ( ! empty( $products_attached ) && $products_attached->count() > 0
        || ! empty( $products_attached_tasks) && $products_attached_tasks->count() > 0
        || ! empty( $products_attached_expenses) && $products_attached_expenses->count() > 0
    ) {
    $products = [];
    $total_tax = 0;
    $total_discount = 0;
    $total_products_amount = 0;
    $sub_total = 0;
    $grand_total = 0;
  }

  if ( ! empty( $products_attached ) && $products_attached->count() > 0 ) {
    $products = array();
    $products['total_tax'] = $total_tax;
    $products['total_discount'] = $total_discount;
    //$products['products_amount'] = $products_amount;
    $products['sub_total'] = $sub_total;
    $products['grand_total'] = $grand_total;
    $products['discount_format'] = $discount_format;
    $products['tax_format'] = $tax_format;
    $products['cart_tax'] = $cart_tax;
    $products['cart_discount'] = $cart_discount;
    $products['amount_payable'] = $amount_payable;

    foreach ($products_attached as $order ) {
        $product_qty = $order->product_qty;
        $product_price = $order->product_price;

        $products['product_name'][] = $order->product_id;
        $products['product_id'][] = $order->product_id;
        $products['product_qty'][] = $product_qty;            
        $products['product_price'][] = $product_price;

        $product_amount = $product_qty * $product_price;
        $products['products_amount'][] = $product_amount;
        $product_tax = $order->product_tax;
        $tax_type = $order->tax_type;
        $products['product_tax'][] = $product_tax; // Rate
        $products['tax_type'][] = $tax_type;
        if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
            $tax_value = ( $product_amount * $product_tax) / 100;
        } else {
            $tax_value = $product_tax;
        }
        $products['tax_value'][] = $tax_value; // Calculated Tax
        $total_tax += $tax_value;

        $product_discount = $order->product_discount;
        $discount_type = $order->discount_type;
        $products['product_discount'][] = $product_discount; // Rate
        $products['discount_type'][] = $discount_type;
        if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
            $discount_value = ( $product_amount * $product_discount) / 100;
        } else {
            $discount_value = $product_discount;
        }
        $products['discount_value'][] = $discount_value;
        $total_discount += $discount_value;

        $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
        // $product_subtotals[ $i ] = $amount;
        $grand_total += $amount;
        $sub_total +=  $amount + $discount_value;
        $products['product_subtotal'][] = $order->product_subtotal;
        $products['product_amount'][] = $order->product_amount;
        
        
        $products['pid'][] = $order->pid;
        $products['unit'][] = $order->unit;
        $products['hsn'][] = $order->hsn;
        $products['alert'][] = $order->alert;
        $products['stock_quantity'][] = $order->stock_quantity;
        $products['product_ids'][] = $order->product_id;
        $products['product_description'][] = $order->product_description;
    }
    $products['total_tax'] = $total_tax;
    $products['total_discount'] = $total_discount;
    $products['sub_total'] = $sub_total;
    $products['grand_total'] = $grand_total;
  }

  if ( ! empty( $products_attached_tasks) && $products_attached_tasks->count() > 0 ) {                  
        foreach ($products_attached_tasks as $order ) {
            $product_qty = $order->product_qty;
            $product_price = $order->product_price;

            $products['product_name'][] = $order->task_id;
            $products['product_id'][] = $order->task_id;
            $products['product_qty'][] = $product_qty;            
            $products['product_price'][] = $product_price;

            $product_amount = $product_qty * $product_price;
            $products['products_amount'][] = $product_amount;

            $product_tax = $order->product_tax;
            $tax_type = $order->tax_type;
            $products['product_tax'][] = $product_tax; // Rate
            $products['tax_type'][] = $tax_type;
            if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                $tax_value = ( $product_amount * $product_tax) / 100;
            } else {
                $tax_value = $product_tax;
            }
            $products['tax_value'][] = $tax_value; // Calculated Tax
            $total_tax += $tax_value;

            $product_discount = $order->product_discount;
            $discount_type = $order->discount_type;
            $products['product_discount'][] = $product_discount; // Rate
            $products['discount_type'][] = $discount_type;
            if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                $discount_value = ( $product_amount * $product_discount) / 100;
            } else {
                $discount_value = $product_discount;
            }
            $products['discount_value'][] = $discount_value;
            $total_discount += $discount_value;

            $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
            // $product_subtotals[ $i ] = $amount;
            $grand_total += $amount;
            $sub_total +=  $amount + $discount_value;
            $products['product_subtotal'][] = $order->product_subtotal;
            $products['product_amount'][] = $order->product_amount;


            $products['pid'][] = $order->pid;
            $products['unit'][] = $order->unit;
            $products['hsn'][] = $order->hsn;
            $products['alert'][] = $order->alert;
            $products['stock_quantity'][] = $order->stock_quantity;
            $products['product_ids'][] = $order->task_id;
            $products['product_description'][] = $order->product_description;
            $products['product_type'][] = 'task';
        }
        $products['total_tax'] = $total_tax;
        $products['total_discount'] = $total_discount;
        $products['sub_total'] = $sub_total;
        $products['grand_total'] = $grand_total;
    }

    if ( ! empty( $products_attached_expenses ) && $products_attached_expenses->count() > 0 ) {                    
        foreach ( $products_attached_expenses as $order ) {
            $product_qty = $order->product_qty;
            $product_price = $order->product_price;

            $products['product_name'][] = $order->expense_id;
            $products['product_id'][] = $order->expense_id;
            $products['product_qty'][] = $product_qty;            
            $products['product_price'][] = $product_price;

            $product_amount = $product_qty * $product_price;
            $products['products_amount'][] = $product_amount;

            $product_tax = $order->product_tax;
            $tax_type = $order->tax_type;
            $products['product_tax'][] = $product_tax; // Rate
            $products['tax_type'][] = $tax_type;
            if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                $tax_value = ( $product_amount * $product_tax) / 100;
            } else {
                $tax_value = $product_tax;
            }
            $products['tax_value'][] = $tax_value; // Calculated Tax
            $total_tax += $tax_value;

            $product_discount = $order->product_discount;
            $discount_type = $order->discount_type;
            $products['product_discount'][] = $product_discount; // Rate
            $products['discount_type'][] = $discount_type;
            if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                $discount_value = ( $product_amount * $product_discount) / 100;
            } else {
                $discount_value = $product_discount;
            }
            $products['discount_value'][] = $discount_value;
            $total_discount += $discount_value;

            $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
            // $product_subtotals[ $i ] = $amount;
            $grand_total += $amount;
            $sub_total +=  $amount + $discount_value;
            $products['product_subtotal'][] = $order->product_subtotal;
            $products['product_amount'][] = $order->product_amount;


            $products['pid'][] = $order->pid;
            $products['unit'][] = $order->unit;
            $products['hsn'][] = $order->hsn;
            $products['alert'][] = $order->alert;
            $products['stock_quantity'][] = $order->stock_quantity;
            $products['product_ids'][] = $order->expense_id;
            $products['product_description'][] = $order->product_description;
            $products['product_type'][] = 'expense';
        }
        $products['total_tax'] = $total_tax;
        $products['total_discount'] = $total_discount;
        $products['sub_total'] = $sub_total;
        $products['grand_total'] = $grand_total;
        //$products = (Object) $products;
    }
    if ( is_array( $products ) && ! empty( $products['product_name'] ) ) {
        $products = (Object) $products;
    }
  
  ob_start();
  ?>
  <div>
  <table class="inventory"  style="margin-right: 90px; width:100%;">
      <thead>
          <tr style="background-color: #eaeaea;">
              <th><span><?php echo trans('custom.products.item_name') ?></span></th>
              <th><span>
                <?php
                if( ! empty( $invoice->show_quantity_as ) ) {
                  echo $invoice->show_quantity_as;
                } else {
                  echo trans('custom.products.quantity');
                }
                ?>
                </span></th>
              <th><span><?php echo trans('custom.products.rate') ?>
                  
              </span></th>
              <th><span> <?php echo trans('custom.products.tax_percent') ?></span></th>
              <th><span> <?php echo trans('custom.products.tax') ?></span></th>
              <th><span> <?php echo trans('custom.products.discount_percent') ?></span></th>
              <th><span> <?php echo trans('custom.products.discount') ?></span></th>
              <th><span> <?php echo trans('custom.products.amount') ?></span></th>
          </tr>
      </thead>
      <tbody>
      <?php            
      if ( ! empty( $products ) ) {
          $product_names = $products->product_name;
          $total_tax = $products->total_tax;
          $total_discount = $products->total_discount;
          $products_amount = $products->products_amount;
          $sub_total = $products->sub_total;
          $grand_total = $products->grand_total;
          
          $product_qtys = $products->product_qty;
          $product_prices = $products->product_price;

          $product_taxs = $products->product_tax;
          $tax_types = $products->tax_type;
          $tax_values = $products->tax_value;

          $product_discounts = $products->product_discount;
          $discount_types = $products->discount_type;
          $discount_values = $products->discount_value;

          $product_subtotals = $products->product_subtotal;
          $pids = $products->pid;
          $units = $products->unit;
          $hsns = $products->hsn;
          $alerts = $products->alert;
          $stock_quantitys = $products->stock_quantity;
          $product_ids = $products->product_ids;
          $product_descriptions = $products->product_description;
          $product_types = ! empty( $products->product_type ) ? $products->product_type : [];
          if ( empty( $product_names ) ) {
            $product_names = [];
          }
          for( $i = 0; $i < count( $product_names ); $i++ ) {

              $product_name = ! empty( $product_names[ $i ] ) ? $product_names[ $i ] : '';
              $product_type = ! empty( $product_types[ $i ] ) ? $product_types[ $i ] : '';
              if ( 'task' === $product_type ) {
                  $product = \App\ProjectTask::find( $product_name );
                  if ( $product ) {
                      $product_name = $product->name;
                  }
              } elseif( 'expense' === $product_type ) {
                  $product = \App\Expense::find( $product_name );
                  if ( $product ) {
                      $product_name = $product->name;
                  }
              } else {
                  if ( ! is_numeric( $product_name ) ) {
                    $product = \App\Product::where('name', $product_name )->first();
                  } else {
                    $product = \App\Product::find( $product_name );
                  }
                  if ( $product ) {
                      $product_name = $product->name;
                  }
              }

              $product_qty = ! empty( $product_qtys[ $i ] ) ? $product_qtys[ $i ] : '1';
              $product_price = ! empty( $product_prices[ $i ] ) ? $product_prices[ $i ] : '0';
              $product_amount = $product_qty * $product_price;

              $product_tax = ! empty( $product_taxs[ $i ] ) ? $product_taxs[ $i ] : '0'; // Rate.
              $product_tax_display = digiCurrency( $product_tax, $invoice->currency_id );

              $tax_type = ! empty( $tax_types[ $i ] ) ? $tax_types[ $i ] : 'percent';
              // $tax_value = ! empty( $tax_values[ $i ] ) ? $tax_values[ $i ] : '0';
              if ( 'percent' === $tax_type ) {
                  $tax_value = ( $product_amount * $product_tax) / 100;
                  $product_tax_display = $product_tax . ' %';
              } else {
                  $tax_value = $product_tax;
              }

              $product_discount = ! empty( $product_discounts[ $i ] ) ? $product_discounts[ $i ] : '0';
              $product_discount_display = digiCurrency( $product_discount, $invoice->currency_id );
              $discount_type = ! empty( $discount_types[ $i ] ) ? $discount_types[ $i ] : 'percent';
              // $discount_value = ! empty( $discount_values[ $i ] ) ? $discount_values[ $i ] : '0';
              if ( 'percent' === $discount_type ) {
                  $discount_value = ( $product_amount * $product_discount) / 100;
                  $product_discount_display = $product_discount . ' %';
              } else {
                  $discount_value = $product_discount;
              }

              $amount = $product_amount + $tax_value - $discount_value;

              $product_subtotal = ! empty( $product_subtotals[ $i ] ) ? $product_subtotals[ $i ] : '0';
              $pid = ! empty( $pids[ $i ] ) ? $pids[ $i ] : '';
              $unit = ! empty( $units[ $i ] ) ? $units[ $i ] : '';
              $hsn = ! empty( $hsns[ $i ] ) ? $hsns[ $i ] : '';
              $alert = ! empty( $alerts[ $i ] ) ? $alerts[ $i ] : '';
              $stock_quantity = ! empty( $stock_quantitys[ $i ] ) ? $stock_quantitys[ $i ] : '';
              $product_id = ! empty( $product_ids[ $i ] ) ? $product_ids[ $i ] : '';
              $product_description = ! empty( $product_descriptions[ $i ] ) ? $product_descriptions[ $i ] : '';
          ?>
          <tr>
              <td style="border: 1px solid lightgray;"><span><?php echo $product_name?></span></td>
              <td style="border: 1px solid lightgray;"><span><?php echo $product_qty?></span></td>
              <td style="border: 1px solid lightgray;"><span data-prefix><?php echo digiCurrency( $product_amount, $invoice->currency_id )?></span></td>
              <td style="border: 1px solid lightgray;"><span data-prefix><?php echo $product_tax_display?></span></td>
              <td style="border: 1px solid lightgray;"><span data-prefix><?php echo digiCurrency($tax_value, $invoice->currency_id)?></span></td>
              <td style="border: 1px solid lightgray;"><span data-prefix><?php echo $product_discount_display?></span></td>
              <td style="border: 1px solid lightgray;"><span data-prefix><?php echo digiCurrency($discount_value, $invoice->currency_id)?></span></td>
              <td style="border: 1px solid lightgray;"><span data-prefix><?php echo digiCurrency($amount, $invoice->currency_id)?></span></td>
          </tr>
          <?php
          }
      }
      ?>
      </tbody>
  </table>
<br/>
  <table class="balance" style="float: right;
border: 1px solid lightgray;
width: 100%;
text-align: right;">
          <tr>
              <th style="border:1px solid lightgray;"><span><?php echo trans('custom.products.total_tax') ?></span></th>
              <td style="border:1px solid lightgray;"><span data-prefix><?php echo digiCurrency($total_tax, $invoice->currency_id) ?></span></td>
          </tr>
          <tr>
              <th style="border:1px solid lightgray;"><span><?php echo trans('custom.products.sub_total') ?></span></th>
              <td style="border:1px solid lightgray;"><span data-prefix><?php echo digiCurrency($sub_total, $invoice->currency_id) ?></span></td>
          </tr>
          <tr>
              <th style="border:1px solid lightgray;"><span><?php echo trans('custom.products.total_discount') ?></span></th>
              <td style="border:1px solid lightgray;"><span data-prefix><?php echo digiCurrency($total_discount, $invoice->currency_id) ?></span></td>
          </tr>
          <tr>
              <th style="border:1px solid lightgray;"><span><?php echo trans('custom.products.grand_total') ?></span></th>
              <td style="border:1px solid lightgray;"><span data-prefix><?php echo digiCurrency($grand_total, $invoice->currency_id) ?></span></td>
          </tr>
          <?php
          $additionals = false;
          if ( ! empty( $products->cart_tax ) && $products->cart_tax > 0 ) {
              $additionals = true;
          ?>
          <tr>
              <th><span><?php ('custom.products.additional-tax') ?></span></th>
              <td><span data-prefix><?php echo digiCurrency($products->cart_tax, $invoice->currency_id) ?></span></td>
          </tr>
          <?php } ?>

          <?php
          if ( ! empty( $products->cart_discount ) && $products->cart_discount > 0 ) {
                  $additionals = true;
          ?>
          <tr>
              <th><span><?php ('custom.products.additional-discount') ?></span></th>
              <td><span data-prefix><?php echo digiCurrency($products->cart_discount, $invoice->currency_id) ?></span></td>
          </tr>
          <?php } ?>

          <?php
          if ( true === $additionals ) {
          ?>
          <tr>
              <th><span><?php ('custom.products.amount-payable') ?></span></th>
              <td><span data-prefix><?php echo digiCurrency($products->amount_payable, $invoice->currency_id) ?></span></td>
          </tr>
      <?php } ?>
          <tr>
              <th><span><?php echo trans('custom.invoices.total-paid') ?></span></th>
              <td>
   <?php
              //$total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', '=', $invoice->id)->sum('amount');
              $amount_due = $invoice->amount - ( $total_paid + $total_used );
              ?>
                  <span data-prefix><?php echo digiCurrency( $total_paid, $invoice->currency_id ) ?></span></td>
          </tr>
          <tr>
              <th><span><?php echo trans('custom.invoices.amount-due') ?></span></th>
              <td><span data-prefix><?php echo digiCurrency( $amount_due, $invoice->currency_id ) ?></span></td>
          </tr>
      </table>
    </div>
  <?php
  return ob_get_clean();
}

function getCurrentDateFormat( $type = 'web' ) {
  $date_set = config('app.date_format');   
  $date_format_setting = getSetting( 'date_format', 'site_settings' );
  // echo $date_format_setting . '@@';
  if ( 'yyyy-mm-dd' === $date_format_setting ){
    $date_set = 'Y-m-d';
    if ( 'moment' === $type ) {
      $date_set = 'YYYY-MM-DD H:m:s';
    }
  } elseif( 'mm-dd-yyyy'=== $date_format_setting ){
    $date_set = 'm-d-Y';
    if ( 'moment' === $type ) {
      $date_set = 'MM-DD-YYYY H:m:s';
    }
  } elseif( 'dd-mm-yyyy'=== $date_format_setting ){
    $date_set = 'd-m-Y';
    if ( 'moment' === $type ) {
      $date_set = 'DD-MM-YYYY H:m:s';
    }
  }
  return $date_set;
}

function getNextNumber( $type = 'Invoice' ) {
  $invoice_no = 0;
  $next_id_generation_type  = getOption('next_id_generation_type'); //countofrecords, maxdb, maxofinvoiceid. Default value will be 'maxdb'
  switch ( $type ) {
    case 'Proposal':
      if ( 'countofrecords' === $next_id_generation_type ) {
        $invoice_no = \Modules\Proposals\Entities\Proposal::count('id');
      } elseif ( 'maxofinvoiceid' === $next_id_generation_type ) {
        $max_id_query = \Modules\Proposals\Entities\Proposal::selectRaw('MAX(invoice_no) as maxid')->first();
        if ( $max_id_query ) {
          $invoice_no = $max_id_query->maxid;
        }
      } else {
        $invoice_no = \Modules\Proposals\Entities\Proposal::max('id');
      }
      if ( empty( $invoice_no ) ) { // If there are no records, the above function will return 'null'
          $invoice_no = 0;
      }
      $invoice_no++;
      $invoice_start = getSetting( 'proposal_start', 'proposal-settings' );
      if ( is_numeric( $invoice_start ) ) {
          $invoice_no = $invoice_start + $invoice_no;
      }
      break;

      case 'Contract':
      if ( 'countofrecords' === $next_id_generation_type ) {
        
        $invoice_no = \Modules\Contracts\Entities\Contract::count('id');
        
      } elseif ( 'maxofinvoiceid' === $next_id_generation_type ) {
        
        $max_id_query = \Modules\Contracts\Entities\Contract::selectRaw('MAX(invoice_no) as maxid')->first();
        
        if ( $max_id_query ) {
          $invoice_no = $max_id_query->maxid;
        }
      } else {
        $invoice_no = \Modules\Contracts\Entities\Contract::max('id');
        
      }
      if ( empty( $invoice_no ) ) { // If there are no records, the above function will return 'null'
          $invoice_no = 0;
      }
      $invoice_no++;
      $invoice_start = getSetting( 'contract_start', 'contract-settings');
      
      if ( is_numeric( $invoice_start ) ) {
          $invoice_no = $invoice_start + $invoice_no;
      }
      break;
      
    case 'Quote':
      if ( 'countofrecords' === $next_id_generation_type ) {
        $invoice_no = \Modules\Quotes\Entities\Quote::count('id');
      } elseif ( 'maxofinvoiceid' === $next_id_generation_type ) {
        $max_id_query = \Modules\Quotes\Entities\Quote::selectRaw('MAX(invoice_no) as maxid')->first();
        if ( $max_id_query ) {
          $invoice_no = $max_id_query->maxid;
        }
      } else {
        $invoice_no = \Modules\Quotes\Entities\Quote::max('id');
      }
      if ( empty( $invoice_no ) ) { // If there are no records, the above function will return 'null'
          $invoice_no = 0;
      }
      $invoice_no++;
      $invoice_start = getSetting( 'quote_start', 'quote-settings' );
      if ( is_numeric( $invoice_start ) ) {
          $invoice_no = $invoice_start + $invoice_no;
      }
      break;
    //credit note
    case 'Credit':
      if ( 'countofrecords' === $next_id_generation_type ) {
        $invoice_no = \App\CreditNote::count('id');
      } elseif ( 'maxofinvoiceid' === $next_id_generation_type ) {
        $max_id_query = \App\CreditNote::selectRaw('MAX(invoice_no) as maxid')->first();
        if ( $max_id_query ) {
          $invoice_no = $max_id_query->maxid;
        }
      } else {
        $invoice_no = \App\CreditNote::max('id');
      }
      if ( empty( $invoice_no ) ) { // If there are no records, the above function will return 'null'
          $invoice_no = 0;
      }
      $invoice_no++;
      $invoice_start = getSetting( 'credit_note_start', 'credit-note-settings' );
      if ( is_numeric( $invoice_start ) ) {
          $invoice_no = $invoice_start + $invoice_no;
      }
      break;
      //end credit note
    case 'PO':
      if ( 'countofinvoices' === $next_id_generation_type ) {
          $invoice_no = \App\PurchaseOrder::count('id');
      } elseif ( 'maxofinvoiceid' === $next_id_generation_type ) {
          $max_id_query = \App\PurchaseOrder::selectRaw('MAX(invoice_no) as maxid')->first();
          if ( $max_id_query ) {
            $invoice_no = $max_id_query->maxid;
          }
      } else {
          $invoice_no = \App\PurchaseOrder::max('id');
      }

      if ( empty( $invoice_no ) ) { // If there are no records, the above function will return 'null'
          $invoice_no = 0;
      }
      $invoice_no++;
      $invoice_start = getSetting( 'Po_Start', 'purchase-orders-settings' );        
      if ( is_numeric( $invoice_start ) ) {
          $invoice_no = $invoice_start + $invoice_no;
      }
      break;
    default:
        if ( 'countofinvoices' === $next_id_generation_type ) {
            $invoice_no = DB::table('invoices')->count('id');
        } elseif ( 'maxofinvoiceid' === $next_id_generation_type ) {
            $max_id_query = DB::table('invoices')->selectRaw('MAX(invoice_no) as maxid')->first();            
            if ( $max_id_query ) {
              $invoice_no = $max_id_query->maxid;
            }
        } else {
            $invoice_no = DB::table('invoices')->max('id');
        }
        
        if ( empty( $invoice_no ) ) { // If there are no records, the above function will return 'null'
            $invoice_no = 0;
        }

        $invoice_no++;
        $invoice_start = getSetting( 'invoice_start', 'invoice-settings' );
        if ( is_numeric( $invoice_start ) ) {
            $invoice_no = $invoice_start + $invoice_no;
        }
      break;
  }
  return $invoice_no;
}

function displayInvoiceNo( $record, $type = 'Invoice' ) {
  $invoice_number_format = getSetting( 'invoice-number-format', 'invoice-settings' );
  $invoice_number_separator = getSetting( 'invoice-number-separator', 'invoice-settings' );
  $invoice_number_length = getSetting( 'invoice-number-length', 'invoice-settings' );

  return $invoice_no;
}

function haveTransactions( $contact_id ) {
  $invoice_payments = DB::table('invoice_payments')->join('invoices', 'invoices.id', '=', 'invoice_payments.invoice_id')->where('payment_status', 'success')->where('invoices.customer_id', $contact_id)->count(); // Invoices and Recurring Invoices
  
  $purchase_order_payments =  DB::table('purchase_orders_payments')->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_orders_payments.purchase_order_id')->where('payment_status', 'Success')->where('purchase_orders.customer_id', $contact_id)->count();

  $order_payments = DB::table('orders_payments')->join('orders', 'orders.id', '=', 'orders_payments.order_id')->where('payment_status', 'success')->where('orders.customer_id', $contact_id)->count();

  $credit_note_payments = DB::table('credit_note_payments')->join('credit_notes', 'credit_notes.id', '=', 'credit_note_payments.credit_note_id')->where('payment_status', 'success')->where('credit_notes.customer_id', $contact_id)->count();

  $credit_note_used = DB::table('credit_note_credits')
    ->join('credit_notes', 'credit_notes.id', '=', 'credit_note_credits.credit_note_id')
    ->join('invoices', 'invoices.id', '=', 'credit_note_credits.invoice_id')
    ->where('invoices.customer_id', $contact_id)->count();

  if ( $invoice_payments > 0 
    || $purchase_order_payments > 0 
    || $order_payments > 0 
    || $credit_note_payments > 0 
    || $credit_note_used > 0
  ) {
    return true;
  } else {
    return false;
  }
}

function digiUpdateAccount( $account_id, $amount, $operation = 'inc' )
{
  // This will avoid the error when the amount is less than zero.
  if ( 'desc' === $operation ) {
    DB::select("UPDATE  ".env('DB_PREFIX')."accounts SET initial_balance = GREATEST(0, initial_balance - $amount) WHERE   id = $account_id");
  } else {
    DB::select("UPDATE  ".env('DB_PREFIX')."accounts SET initial_balance = GREATEST(0, initial_balance + $amount) WHERE   id = $account_id");
  }
}

function digiUpdateProduct( $product_id, $quantity, $operation = 'inc' )
{
  // This will avoid the error when the amount is less than zero.
  if ( 'desc' === $operation ) {
    DB::select("UPDATE  ".env('DB_PREFIX')."products SET stock_quantity = GREATEST(0, stock_quantity - $quantity) WHERE   id = $product_id");
  } else {
    DB::select("UPDATE  ".env('DB_PREFIX')."products SET stock_quantity = GREATEST(0, stock_quantity + $quantity) WHERE   id = $product_id");
  }
}

function is_decimal( $val )
{
    return is_numeric( $val ) && floor( $val ) != $val;
}

function getLanguages()
{
  
  $session_languages = json_decode(session('languages'));
  if ( ! empty( $session_languages ) ) {
      return $session_languages;
  } else {
      $session_languages = updateLanguages();
      return $session_languages;
  }
}

function updateLanguages()
{
  $session_languages = \App\Language::select(['code', 'language', 'is_rtl'])->get()->toArray();
  if ( empty( $session_languages ) ) {
    $lang = [
      'code' => 'en',
      'language' => 'English',
      'is_rtl' => 'No',
    ];
    $session_languages[] = $lang;
    \App\Language::create( $lang );
  }
  session()->put('languages', json_encode($session_languages));
  return json_decode(session('languages'));
}

function getPlugins()
{
  
  $session_plugins = json_decode(session('plugins'));
  if ( ! empty( $session_plugins ) ) {
      return $session_plugins;
  } else {
      $session_plugins = updateLanguages();
      return $session_plugins;
  }
}

function isPluginActive( $slug )
{
  
  if ( config('app.db_database') == '' ) {
    return false;
  }
  if ( config('app.db_database') == '' ) {
    return false;
  }
  $session_plugins = (array) json_decode(session('plugins'));

  if ( empty( $session_plugins ) ) {
    $session_plugins = updatePlugins();
  }

  if ( is_array( $slug ) ) {
    foreach( $slug as $p ) {
      if ( ! empty( $session_plugins[ $p ] ) ) {
        return true;
      }
    }
  } elseif ( ! empty( $session_plugins[ $slug ] ) ) {
      return true;
  } else {
      $plugin = \Modules\ModulesManagement\Entities\ModulesManagement::where('enabled', 'Yes')->where('slug', $slug)->first();
      if ( $plugin ) {
        return true;
      }
  }

  return false;
}

function getLocalSetting( $module, $key, $default = '' ) {
  
  $local_settings = (array) json_decode(session('local_settings'), true);

  if ( empty( $local_settings ) ) {
    $local_settings = updateLocalSettings();
  }

  if ( ! empty( $local_settings[ $module ][ $key ] ) ) {
    return $local_settings[ $module ][ $key ];
  } else {
    return $default;
  }
}

function updateLocalSettings( $module = '' )
{
  $default_currency = $user_currency = App\Currency::where('is_default', 'yes')->first();
  if ( ! $default_currency ) {
    $default_currency = $user_currency = App\Currency::where('id', DEFAULT_CURRENCY_ID)->first();
  }
  $default_currency = $default_currency->toArray();
  $contact = \App\Contact::where( 'id', Auth::id())->first();
  if ( $contact && $contact->currency ) {
      $user_currency = $contact->currency->toArray();          
  }
  $options = App\Option::get()->pluck('name', 'value')->toArray();

  $currencies = App\Currency::where('status', 'Active')->get();
  $currencies_arr = [];
  if ( $currencies->count() > 0 ) {
    foreach ($currencies as $curr) {
      $currencies_arr[ $curr->id ] = $curr;
    }
  }

  $local_settings = [
    'default_currency' => $default_currency,
    'user_currency' => $user_currency,
    'options' => $options,
    'currencies' => $currencies_arr,
  ];
  session()->put('local_settings', json_encode($local_settings));
  return (array) json_decode(session('local_settings'), true);
}

function getCache( $type, $cache_key, $default = '' )
{
  $parts = explode('_', $cache_key);
  $id = $parts[ count( $parts ) - 1 ];
  switch ( $type ) {
    case 'invoice_total_paid':
      $total_paid = cache($cache_key, null);
      if ( null === $total_paid ) {          
          if ( is_numeric( $id ) ) {
            $total_paid =  \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', $id)->where('payment_status', 'Success')->sum('amount');
            addCache( $cache_key, $total_paid);
          }
      }
      if ( empty( $total_paid ) ) {
        $total_paid = $default;
      }
      return $total_paid;
    break;

    case 'credit_note_total_paid':
      $total_paid = cache($cache_key, null);
      if ( null === $total_paid ) {
          if ( is_numeric( $id ) ) {
            $total_paid =  \App\CreditNotePayment::where('credit_note_id', '=', $id)->where('payment_status', 'Success')->sum('amount');
            addCache( $cache_key, $total_paid);
          }
      }
      if ( empty( $total_paid ) ) {
        $total_paid = $default;
      }
      return $total_paid;
    break;

    case 'applied_credits_invoice':
      $total_paid = cache($cache_key, null);
      if ( null === $total_paid ) {
          if ( is_numeric( $id ) ) {
            $total_paid =  DB::table('credit_note_credits')->where('invoice_id', $id)->sum('amount');
            addCache( $cache_key, $total_paid);
          }
      }
      if ( empty( $total_paid ) ) {
        $total_paid = $default;
      }
      return $total_paid;
    break;

    case 'credit_note_total_used':
      $total_paid = cache($cache_key, null);
      if ( null === $total_paid ) {
          if ( is_numeric( $id ) ) {
            $total_paid =  \App\CreditNoteCredit::where('credit_note_id', '=', $id)->sum('amount');
            addCache( $cache_key, $total_paid);
          }
      }
      if ( empty( $total_paid ) ) {
        $total_paid = $default;
      }
      return $total_paid;
    break;
  }
  return $default;
}

function appliedCreditsInvoice( $invoice_id, $credit_note_id )
{
  $applied_credits = cache('applied_credits_' . $invoice_id . '_' . $credit_note_id, null);
  if ( null === $applied_credits ) {
    $applied_credits = DB::table('credit_note_credits')->where('invoice_id', $invoice_id)->where('credit_note_id', $credit_note_id)->sum('amount');
  }
  return ( ! empty( $applied_credits ) ) ? $applied_credits : 0;
}

function addCache( $cache_key, $value )
{
  cache([$cache_key => $value], now()->addMinutes(CACHE_MINUTES));
}



function updatePlugins()
{
  $session_plugins = \Modules\ModulesManagement\Entities\ModulesManagement::select(['slug', 'type'])->where('enabled', 'Yes')->get()->pluck('type', 'slug')->toArray();
  session()->put('plugins', json_encode($session_plugins));
  return (array) json_decode(session('plugins'));
}

function validateRequirements()
{
  $requirements = [
      'phpversion' => 'PHP Version >= 7.1.3',
      'env' => '.env Writable',
      'PDO' => 'PDO PHP Extension',
      'Ctype' => 'Ctype PHP Extension',
      'JSON' => 'JSON PHP Extension',
      'BCMath' => 'BCMath PHP Extension',
      'max_execution_time' => 'max_execution_time',
      'tokenizer' => 'Tokenizer PHP Extension',
      'xml' => 'XML PHP Extension',
      'gd' => 'GD Library',
      'fileinfo' => 'fileinfo',
      'openssl' => 'openssl',
      'mbstring' => 'Mbstring PHP Extension',
    ];

    $isInstallable = true;
    $message = '';
    foreach ($requirements as $key => $desc) {
      if ( 'phpversion' == $key ) {
        if (version_compare(phpversion(), '7.1.3', '<')) {
          $isInstallable = false;
          $message .= $desc . '##';
        }
      } elseif ( 'max_execution_time' == $key ) {
        if( ! ini_get('max_execution_time') ) {
          $isInstallable = false;
          $message .= $desc . '##';
        }
      } elseif ( 'env' == $key ) {
        if ( ! is_writable( base_path() . '/.env' ) ) {
          $isInstallable = false;
          $message .= $desc . '##';
        }
      } else {
        if (! extension_loaded( $key ) ) {
          $isInstallable = false;
          $message .= $desc . '##';
        }
      }
    }
    return  ['status' => $isInstallable, 'message' => $message];
}

function isSame($current, $previous)
{
  return \Illuminate\Support\Str::contains($previous, $current);
}