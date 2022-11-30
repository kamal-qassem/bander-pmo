<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Modules\Orders\Entities\Order;
use App\Option;
use \Datetime;
use Modules\Templates\Entities\Template;
use Location;
use Carbon\Carbon;
use App\InternalNotification;

defined('CRON_JOB') or define('CRON_JOB', 'Yes');

class OrderReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This includes Recurring orders Reminders and generation of new order for recurring orders, based on settings.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->runCron();

        $this->info('order reminders Code executed');
    }

    public function runCron() {
        updateOption('last_cron_run', time());

	    // $this->overduereminders();

        $this->generateorder();
    }

    private function overduereminders() {
    	
        $create_neworder_from_only_on_paid_orders = getSetting('create_neworder_from_only_on_paid_orders', 'order-cronjob');

        $invoices = Order::where('is_recurring', 'yes')->whereNotNull('recurring_value');
        if ( 'yes' === $create_neworder_from_only_on_paid_orders ) {
            $invoices->where('status', 'Active');
        }
        $invoices->get();

        foreach ($invoices as $invoice) {
            if ($invoice->prevent_overdue_reminders == 'no' && $this->isTemplateAvailable( 'order-overdue-notice' )) {
                if ( $invoice->last_overdue_reminder ) {
                    // We already have sent reminder, check for resending
                    $resend_days = getSetting('automatically_resend_invoice_overdue_reminder_after', 'order-cronjob');
                    // If resend_days from options is 0 means that the admin dont want to resend the mails.
                    if ( ! empty( $resend_days ) ) {
                        $datediff  = $now - strtotime($invoice->last_overdue_reminder);
                        $days_diff = floor($datediff / (60 * 60 * 24));
                        if ($days_diff >= $resend_days) {
                            $customer = $invoice->customer()->first();
                            if ( $customer ) {
                                $action = 'order-overdue-notice';
                                $data = array();
                                $data['client_name'] = $customer->first_name . ' ' . $customer->last_name;
                                $data['to_email'] = $customer->email;
                                // $data['content'] = '';
                                $data['site_title'] = getSetting( 'site_title', 'site_settings');
                                $logo = getSetting( 'site_logo', 'site_settings' );
                                $data['logo'] = asset( 'uploads/settings/' . $logo );

                                $data['date'] = digiTodayDateAdd();
                                $data['invoice_url'] = route( 'admin.orders.show',$invoice->id );
                                $data['invoice_no'] = $invoice->id;
                                $data['invoice_amount'] = $invoice->price;
                                $data['invoice_due_date'] = digiDate( $invoice->invoice_due_date );

                                $data['site_address'] = getSetting( 'site_address', 'site_settings');
                                $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
                                $data['site_email'] = getSetting( 'contact_email', 'site_settings');   
                                if ( env('MAIL_HOST') != 'smtp.mailtrap.io' ) {
                                    sendEmail( $action, $data );
                                }                                
                            }
                        }
                    }
                }
            }
        }        
    }

    private function generateorder() {
        
        $create_neworder_from_only_on_paid_orders = getSetting('create_neworder_from_only_on_paid_orders', 'order-cronjob');
        $generate_invoice = getSetting('generate_invoice', 'order-cronjob');

        $orders_query = Order::where('is_recurring', 'yes')->whereNotNull('recurring_value');
        if ( 'yes' === $create_neworder_from_only_on_paid_orders ) {
            $orders_query->where('status', 'Active');
        }
        $orders = $orders_query->get()->toArray();

        $_renewals_ids_data = [];
        $total_renewed      = 0;

        foreach ($orders as $order) {
            // Current date
            $date = new DateTime(date('Y-m-d'));
            // Check if is first recurring
            if (!$order['last_recurring_date']) {
                $last_recurring_date = date('Y-m-d', strtotime($order['invoice_date']));
            } else {
                $last_recurring_date = date('Y-m-d', strtotime($order['last_recurring_date']));
            }
            
            $re_create_at = date( 'Y-m-d', time() );

            if ( ! empty( $order['recurring_value'] ) && ! empty( $order['recurring_type'] ) ) {
                $re_create_at = date('Y-m-d', strtotime('+' . $order['recurring_value'] . ' ' . strtoupper($order['recurring_type']), strtotime($last_recurring_date)));
            }

            if ( strtotime( date('Y-m-d') ) >= strtotime( $re_create_at ) ) {
                // Recurring order date is okey lets convert it to new order
                $_order = order::find( $order['id'] );

                $neworder = $_order->replicate();

                $neworder->slug = md5(microtime());
                $neworder->is_recurring = 'no';
                $neworder->recurring_type = NULL;
                $neworder->recurring_value = NULL;
                $neworder->last_recurring_date = NULL;
                $neworder->billing_cycle_id = NULL;
                $neworder->total_cycles = 0;
                $neworder->cycles = 0;

                $neworder->is_recurring_from = $_order->id;

                if ($_order->invoice_due_date) {
                    // Now we need to get duedate from the old order and calculate the time difference and set new duedate
                    // Ex. if the first order had duedate 20 days from now we will add the same duedate date but starting from now
                    $dStart                      = new DateTime($order['invoice_date']);
                    $dEnd                        = new DateTime($order['invoice_due_date']);
                    $dDiff                       = $dStart->diff($dEnd);
                    $neworder->invoice_due_date = date('Y-m-d', strtotime('+' . $dDiff->days . ' DAY', strtotime($re_create_at)));
                } else {
                    $invoice_due_after = getSetting('invoice_due_after', 'invoice-settings');
                    if ($invoice_due_after != 0) {
                        $neworder->invoice_due_date = date('Y-m-d', strtotime('+' . $invoice_due_after . ' DAY', strtotime($re_create_at)));
                    }
                }
                $neworder->status = 'Pending';
                $neworder->save();

                $customer = $neworder->customer()->first();

                $_order->last_recurring_date = $re_create_at;
                $_order->total_cycles = $_order->total_cycles + 1;
                $_order->save();

                if ( $customer && 'yes' === $generate_invoice ) {
                    
                    $delivery_address_raw = ( $customer->delivery_address ) ? json_decode( $customer->delivery_address, true ) : array();            
                    $delivery_address = '';
                    if ( ! empty( $delivery_address_raw['first_name'] ) ) {
                        $delivery_address .= $delivery_address_raw['first_name'];
                        
                        if ( ! empty( $delivery_address_raw['last_name'] ) ) {
                            $delivery_address .= ' ' . $delivery_address_raw['last_name'];
                        }
                    } else {
                        $delivery_address .= $customer->first_name;
                    }
                    
                    if ( ! empty( $delivery_address_raw['address'] ) ) {
                        $delivery_address .= '<br>' . $delivery_address_raw['address'];
                    } else {
                        $delivery_address .= '<br>' . $customer->address;
                    }
                    
                    if ( ! empty( $delivery_address_raw['city'] ) ) {
                        $delivery_address .= '<br>' . $delivery_address_raw['city'];
                    } else {
                        $delivery_address .= '<br>' . $customer->city;
                    }
                    
                    if ( ! empty( $delivery_address_raw['state_region'] ) ) {
                        $delivery_address .= '<br>' . $delivery_address_raw['state_region'];
                    } else {
                        $delivery_address .= '<br>' . $customer->state_region;
                    }
                    
                    if ( ! empty( $delivery_address_raw['country'] ) ) {
                        $country = \App\Country::find( $delivery_address_raw['country'] );
                        $country_name = '';
                        if ( $country ) {
                            $country_name = $country->title;
                        }
                        if ( ! empty( $country_name ) ) {
                            $delivery_address .= '<br>' . $delivery_address_raw['country'];
                        }
                    } else {
                        $country = \App\Country::find( $customer->country_id );
                        $country_name = '';
                        if ( $country ) {
                            $country_name = $country->title;
                        }
                        if ( ! empty( $country_name ) ) {
                            $delivery_address .= '<br>' . $customer->country_name;
                        }
                    }
                    if ( ! empty( $delivery_address_raw['zip_postal_code'] ) ) {
                        $delivery_address .= '-' . $delivery_address_raw['zip_postal_code'];
                    } else {
                        $delivery_address .= '-' . $customer->zip_postal_code;
                    }

                    $amount_payable = $neworder->price;
                    $products_details = ! empty( $neworder->products_details ) ? json_decode($neworder->products_details) : array();

                    $new_invoice_action = getSetting('new_invoice_action', 'order-cronjob');
                    $status = 'Published';
                    $paymentstatus = 'unpaid';
                    if ( 'generate_draft_invoice' === $new_invoice_action ) {
                        $status = 'Draft';
                    }

                    $data = [
                        'slug' => md5(microtime() . rand()),
                        // 'title' => trans('custom.messages.order-title'),
                        'invoice_no' => getNextNumber(),
                        'address' => $customer->fulladdress,
                        'invoice_prefix' => getSetting( 'invoice-prefix', 'invoice-settings' ),
                        'show_quantity_as' => getSetting( 'show_quantity_as', 'invoice-settings' ),
                        'status' => $status,
                        'invoice_date' => date('Y-m-d'),
                        'invoice_due_date' => date('Y-m-d'),
                        'customer_id' => $customer->id,
                        'currency_id' => $neworder->currency_id,
                        'amount' => $amount_payable,
                        'products' => json_encode( $products_details ),
                        'paymentstatus' => $paymentstatus,
                        //'created_by_id' => Auth::id(),
                        'delivery_address' => $delivery_address,
                        'order_id' => $neworder->id,
                    ];
                    $data['invoice_number_format'] = getSetting( 'invoice-number-format', 'invoice-settings', 'numberbased' );
                    $data['invoice_number_separator'] = getSetting( 'invoice-number-separator', 'invoice-settings', '-' );
                    $data['invoice_number_length'] = getSetting( 'invoice-number-length', 'invoice-settings', '0' );
                    $newinvoice = \App\Invoice::create( $data );

                    if ( 'generate_and_send' === $new_invoice_action ) {
                        $customer = $newinvoice->customer()->first();
                        $action = 'invoice-created';
                        $data = array(
                            'client_name' => $customer->first_name . ' ' . $customer->last_name,
                            'to_email' => $customer->email,
                        );
                        // $data['content'] = '';
                        $data['site_title'] = getSetting( 'site_title', 'site_settings');
                        $logo = getSetting( 'site_logo', 'site_settings' );
                        $data['logo'] = asset( 'uploads/settings/' . $logo );
                        $data['date'] = digiTodayDateAdd();

                        $data['invoice_url'] = route( 'admin.invoices.preview', [ 'slug' => $newinvoice->slug ] );
                        $data['invoice_no'] = $newinvoice->invoice_no;
                        $data['invoice_amount'] = $newinvoice->amount;
                        $data['invoice_date'] = digiDate( $newinvoice->invoice_date );
                        $data['invoice_due_date'] = digiDate( $newinvoice->invoice_due_date );

                        $data['site_address'] = getSetting( 'site_address', 'site_settings');
                        $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
                        $data['site_email'] = getSetting( 'contact_email', 'site_settings'); 

                        if ( env('MAIL_HOST') != 'smtp.mailtrap.io' ) {
                            sendEmail( $action, $data );
                        }
                        $this->insertHistory( array('id' => $newinvoice->id, 'comments' => $action, 'operation_type' => 'cron-email' ) );
                    }
                }
            }
        }       
    }

    private function insertHistory( $data ) {
        $ip_address = GetIP();
        $position = Location::get( $ip_address );

        $id = ! empty( $data['id'] ) ? $data['id'] : 0;
        $comments = ! empty( $data['comments'] ) ? $data['comments'] : 0;
        $operation_type = ! empty( $data['operation_type'] ) ? $data['operation_type'] : 'general';

        $city = ! empty( $position->cityName ) ? $position->cityName : '';
        if ( ! empty( $position->regionName ) ) {
            $city .= ' ' . $position->regionName;
        }
        if ( ! empty( $position->zipCode ) ) {
            $city .= ' ' . $position->zipCode;
        }
        $log = array(
            'ip_address' => $ip_address,
            'country' => ! empty( $position->countryName ) ? $position->countryName : '',
            'city' => $city,
            'browser' => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Cron job',
            'invoice_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \App\InvoicesHistory::create( $log );
    }

    private function isTemplateAvailable( $key ) {
        $template = Template::where('key', '=', $key)->where('status', '=', 'active')->first();
        if ( $template ) {
            return true;
        } else {
            return false;
        }
    }
}
