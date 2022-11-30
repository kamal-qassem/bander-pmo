<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Eventy;
use App\Option;
use Modules\RecurringInvoices\Entities\RecurringInvoice as RecurringInvoiceModel;
use \Datetime;
use Location;

defined('CRON_JOB') or define('CRON_JOB', 'Yes');

class RecurringInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurringinvoice:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recurring invoice will generate automatically based on recurring invoice settings.';

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
        //
        $last_cron_run  = getOption('last_cron_run');

        $seconds = Eventy::filter('recurringinvocie.cron_functions_execute_seconds', 300); // Minimum 5 Minutes.


        if ($last_cron_run == '' || (time() > ($last_cron_run + $seconds))) {
            $this->runCron();
        }

        $this->info('Recurring invoice Code executed');
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

    public function runCron() {
        updateOption('last_cron_run', time()); 

       
        // If we have handled this at kernel level so no need to check here.
        /*
        $invoice_hour_auto_operations = getSetting('invoice_auto_operations_hour', 'invoice-cronjob'); // When the cron job will run

        if ($invoice_hour_auto_operations == '') {
            $invoice_hour_auto_operations = 9;
        }

        $invoice_hour_auto_operations = intval( $invoice_hour_auto_operations );
        $hour_now                     = date('G');
        
        if ($hour_now != $invoice_hour_auto_operations ) {
            return;
        }
        */
        
       

        $new_recurring_invoice_action = getSetting('new_recurring_invoice_action', 'invoice-cronjob');
        $invoices_create_invoice_from_recurring_only_on_paid_invoices = getSetting('invoices_create_invoice_from_recurring_only_on_paid_invoices', 'invoice-cronjob');

        $invoices_query = RecurringInvoiceModel::whereRaw('(cycles != total_cycles OR cycles=0)');
        if ($invoices_create_invoice_from_recurring_only_on_paid_invoices == 'yes') {
            // Includes all recurring invoices with paid status if this option set to Yes
            $invoices_query->where('paymentstatus', '=', 'paid');
        }
        $invoices_query->where('status', '=', 'Published');

        $invoices = $invoices_query->get()->toArray();

        $_renewals_ids_data = [];
        $total_renewed      = 0;

        foreach ($invoices as $invoice) {
            // Current date
            $date = new DateTime(date('Y-m-d'));
            // Check if is first recurring
            if (!$invoice['last_recurring_date']) {
                $last_recurring_date = date('Y-m-d', strtotime($invoice['invoice_date']));
            } else {
                $last_recurring_date = date('Y-m-d', strtotime($invoice['last_recurring_date']));
            }
            
            $re_create_at = date( 'Y-m-d', time() );

            if ( ! empty( $invoice['recurring_value'] ) && ! empty( $invoice['recurring_type'] ) ) {
                $re_create_at = date('Y-m-d', strtotime('+' . $invoice['recurring_value'] . ' ' . strtoupper($invoice['recurring_type']), strtotime($last_recurring_date)));
            }

            if ( strtotime( date('Y-m-d') ) >= strtotime( $re_create_at ) ) {            
            
                // Recurring invoice date is okey lets convert it to new invoice
                $_invoice                     = RecurringInvoiceModel::find( $invoice['id'] );

                $newinvoice = $_invoice->replicate();

                $newinvoice->slug = md5(microtime());
                $newinvoice->is_recurring = 'no';
                $newinvoice->recurring_type = NULL;
                $newinvoice->recurring_value = NULL;
                $newinvoice->last_recurring_date = NULL;
                $newinvoice->recurring_period_id = NULL;
                $newinvoice->total_cycles = 0;
                $newinvoice->cycles = 0;

                
                $newinvoice->is_recurring_from = $_invoice->id;

                $invoice_no = getNextNumber();
                if ( empty( $invoice_no ) ) { // If there are no records, the above function will return 'null'
                    $invoice_no = 1;
                }
                $newinvoice->invoice_no = $invoice_no;

                if ($_invoice->invoice_due_date) {
                    // Now we need to get duedate from the old invoice and calculate the time difference and set new duedate
                    // Ex. if the first invoice had duedate 20 days from now we will add the same duedate date but starting from now
                    $dStart                      = new DateTime($invoice['invoice_date']);
                    $dEnd                        = new DateTime($invoice['invoice_due_date']);
                    $dDiff                       = $dStart->diff($dEnd);
                    $newinvoice->invoice_due_date = date('Y-m-d', strtotime('+' . $dDiff->days . ' DAY', strtotime($re_create_at)));
                } else {
                    $invoice_due_after = getSetting('invoice_due_after', 'invoice-settings');
                    if ($invoice_due_after != 0) {
                        $newinvoice->invoice_due_date = date('Y-m-d', strtotime('+' . $invoice_due_after . ' DAY', strtotime($re_create_at)));
                    }
                }

                // Determine status based on settings
                if ($new_recurring_invoice_action == 'generate_and_send' || $new_recurring_invoice_action == 'generate_unpaid') {
                    $newinvoice->status = 'Published';
                } elseif ($new_recurring_invoice_action == 'generate_draft') {
                    $newinvoice->status = 'Draft';
                }
                $newinvoice->save();

                $products_sync = RecurringInvoiceModel::select(['pop.*'])
                ->join('invoice_products as pop', 'pop.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'pop.product_id')
                ->where('invoices.id', $invoice['id'])->get()->makeHidden(['invoice_id'])->toArray();
                $newinvoice->invoice_products()->sync( $products_sync );

                $newinvoice->allowed_paymodes()->sync(array_filter((array)$newinvoice->allowed_paymodes()->get()->pluck('id')));

                $this->insertHistory( array('id' => $newinvoice->id, 'comments' => 'invoice-created', 'operation_type' => 'cron-created' ) );


                $_invoice->last_recurring_date = $re_create_at;
                $_invoice->total_cycles = $_invoice->total_cycles + 1;
                $_invoice->save();

                if ($new_recurring_invoice_action == 'generate_and_send') {                   
                    $customer = $newinvoice->customer()->first();
                    if ( $customer ) {
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

                        $data['invoice_url'] = route( 'admin.recurring_invoices.preview', [ 'slug' => $newinvoice->slug ] );
                        $data['invoice_no'] = $newinvoice->invoice_no;
                        $data['invoice_amount'] = $newinvoice->amount;
                        $data['invoice_date'] = digiDate( $newinvoice->invoice_date );
                        $data['invoice_due_date'] = digiDate( $newinvoice->invoice_due_date );
                        $data['products'] = productshtml( $newinvoice->id, 'invoice' );

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
}
