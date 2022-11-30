<?php

namespace App\Console\Commands;

use Eventy;
use App\Option;
use Carbon\Carbon;
use App\InternalNotification;
use DB;


use Illuminate\Console\Command;
use Kordy\Ticketit\Models\Setting;

use Faker\Factory as Faker;
use Location;

defined('CRON_JOB') or define('CRON_JOB', 'Yes');

class FakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This includes Tickets auto close, Delete User actions data';

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

        $this->generate_fakedata();

    }

    private function generate_fakedata() {
        $faker = Faker::create();
    
        $basecurrency = \App\Currency::where('status', '=', 'Active')->where('is_default', 'yes')->first();
        if ( ! $basecurrency ) {
          $basecurrency = \App\Currency::where('status', '=', 'Active')->inRandomOrder()->first();
          $basecurrency->is_default = 'yes';
          $basecurrency->save();          
        }
        
        $products = \App\Product::get();
        foreach( $products as $product ) {
            $currencies =  \App\Currency::where('status', '=', 'Active')->count('id');
            $prices_available = \App\Currency::where('status', '=', 'Active')->inRandomOrder()->take(rand(1, $currencies))->pluck('code')->toArray();
            
            $actual_price = $product->actual_price;
            $sale_price = $product->sale_price;

            $prices = [];
            foreach ($prices_available as $code ) {
              $prices['actual'][ $code ] = $faker->randomFloat(2, 1, 99999);
              $prices['sale'][ $code ] = $faker->randomFloat(2, 1, 99999);
            }

            $product->prices = json_encode( $prices );
            $product->prices_available = implode(',', $prices_available);
            $product->save();
        }
        
        $contacts = \App\Contact::get();
        foreach( $contacts as $contact ) {
            $is_user = $contact->id%20;
            if ( $is_user == 0 ) {
                $contact->is_user = 'yes';
                $contact->password = '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'; // secret
                $contact->department_id = \App\Department::inRandomOrder()->first()->id;
                $contact->hourly_rate = $faker->randomFloat(1, 99);
                $contact->save();
                $user = $contact->fresh();
                $roles = $contact->contact_type->pluck('id')->toArray();
                
                $user->role()->sync($roles);
            }
        }
       
                
        
        // Invoices.
        $number = rand(100, 200);
        for( $i = 0; $i < $number; $i++ ) {
          $yesno = $faker->randomElement(['yes', 'no']);
          $customer = \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', CUSTOMERS_TYPE);
                          })->first();
          $after_days = rand(1,99);
          $paymentstatus = $faker->randomElement(['unpaid', 'paid', 'due', 'partial', 'on-hold', 'rejected', 'cancelled']);
          $amount = $faker->randomFloat(2,1);
          $data = [
              'title' => $faker->word,
              'address' => $faker->address,
              'invoice_prefix' => $faker->sentence( rand(3, 10) ),
              'show_quantity_as' => $faker->randomElement(['Qty', 'Quantity']),
              'invoice_no' => $faker->numberBetween(1,10000 ),
              'status' => $faker->randomElement(['Published', 'Draft']),
              'reference' => $faker->word,
              'invoice_date' => $faker->date('Y-m-d'),
              'invoice_due_date' => $faker->date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$after_days.' days')),
              'invoice_notes' =>$faker->text(200),
              'amount' => $amount,
              'customer_id' => $customer->id,
              'currency_id' => $customer->currency_id,
              'tax_id' => \App\Tax::inRandomOrder()->first()->id,
              'discount_id' => \App\Discount::inRandomOrder()->first()->id,
              'products' => $faker->name,
              'slug' => $faker->slug,
              'delivery_address' => $faker->address,
              'admin_notes' => $faker->text(200),
              'sale_agent' => \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', CONTACT_SALE_AGENT);
                          })->first()->id,
              'terms_conditions' => $faker->text(200),
              'is_recurring' => $yesno,
          ];
          if ( 'yes' === $yesno ) {
              $data['recurring_value'] = $faker->numberBetween(1,50 );
              $data['recurring_type'] = $faker->randomElement(['day', 'week', 'month', 'year']);
          }

          $invoice = \App\Invoice::create( $data );

          $products_sync = $this->randomProducts();
          $invoice->invoice_products()->sync( $products_sync['products'] );
          $invoice->amount = $products_sync['grand_total'];
          $invoice->save();

          $allowed_paymodes = [ \App\PaymentGateway::inRandomOrder()->first()->id ];
          $invoice->allowed_paymodes()->sync( $allowed_paymodes );

          $id = $invoice->id;

          $history = [
            'id' => $id, 
            'comments' => 'invoice-created',
            'operation_type' => 'crud',
          ];
          $this->insertHistory($history, 'Invoice');

          if ( 'unpaid' == $paymentstatus && 'yes' === $yesno ) {
            $history = [
              'id' => $id, 
              'comments' => 'Status change to ' . $paymentstatus,
              'operation_type' => 'crud',
            ];
            $this->insertHistory($history, 'Invoice');
          }

          if ( in_array( $paymentstatus, ['paid', 'partial']) ) {
            $data = array();
            $after_days = rand(1,$after_days);
            
            if ( 'partial' === $paymentstatus ) {
              $amount = $faker->randomFloat(1, $amount);
            }
            $data['date'] = $faker->date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$after_days.' days') );
            $data['amount'] = $amount;
            $data['transaction_id'] = $faker->sentence( rand(3, 10) );
            $data['account_id'] = \App\Account::inRandomOrder()->first()->id;
            $data['invoice_id'] = $id;
            $data['paymentmethod'] = \App\PaymentGateway::where('status', '=', 'Active')->inRandomOrder()->first()->key;
            $data['description'] = $faker->text(200);
            if ( 'partial' === $paymentstatus ) {
              $data['payment_status'] = 'Success';
            }

            \Modules\InvoicePayments\Entities\InvoicePayment::create( $data );
            $this->insertHistory( array('id' => $id, 'comments' => 'invoices-payment-inserted', 'operation_type' => 'payment' ), 'Invoice' );
          }
        }
        
        // Credit Notes.
        $number = rand(10, 50);
        for( $i = 0; $i < $number; $i++ ) {
            $paymentstatus = $faker->randomElement(['unpaid', 'paid', 'due', 'partial', 'on-hold', 'rejected', 'cancelled']);
            $customer = \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', CUSTOMERS_TYPE);
                          })->first();
            $after_days = rand(1,99);
            $total_amount = $faker->randomFloat(2,1);
            $data = [
              'title' => $faker->word,
              'address' => $faker->address,
              'invoice_prefix' => $faker->sentence( rand(3, 10) ),
              'show_quantity_as' => $faker->randomElement(['Qty', 'Quantity']),
              'invoice_no' => $faker->numberBetween(1,10000 ),
              'status' => $faker->randomElement(['Published', 'Draft']),
              'reference' => $faker->word,
              'invoice_date' => $faker->date('Y-m-d'),
              'invoice_due_date' => $faker->date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$after_days.' days')),
              'invoice_notes' =>$faker->text(200),
              'amount' => $total_amount,
              'customer_id' => $customer->id,
              'currency_id' => $customer->currency_id,
              'tax_id' => \App\Tax::inRandomOrder()->first()->id,
              'discount_id' => \App\Discount::inRandomOrder()->first()->id,
              'products' => $faker->name,
              'slug' => $faker->slug,
              'delivery_address' => $faker->address,
              'admin_notes' => $faker->text(200),
              'sale_agent' => \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', CONTACT_SALE_AGENT);
                          })->first()->id,
              'terms_conditions' => $faker->text(200),             
          ];
          $credit_note = \App\CreditNote::create( $data );

          $products_sync = $this->randomProducts();
          $credit_note->credit_note_products()->sync( $products_sync['products'] );
          $credit_note->amount = $products_sync['grand_total'];
          $credit_note->save();

          $id = $credit_note->id;

          $history = [
            'id' => $id, 
            'comments' => 'credit-note-created',
            'operation_type' => 'crud',
          ];
          $this->insertHistory($history, 'CreditNote');

          if ( in_array( $paymentstatus, ['paid', 'partial']) ) {
            $data = array();
            $after_days = rand(1,$after_days);
            
            $amount = $total_amount;
            if ( 'partial' === $paymentstatus ) {
              $amount = $faker->randomFloat(1, $amount);
            }
            $data['date'] = $faker->date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$after_days.' days') );
            $data['amount'] = $amount;
            $data['transaction_id'] = $faker->sentence( rand(3, 10) );
            $data['account_id'] = \App\Account::inRandomOrder()->first()->id;
            $data['credit_note_id'] = $id;
            $data['paymentmethod'] = \App\PaymentGateway::where('status', '=', 'Active')->inRandomOrder()->first()->key;
            $data['description'] = $faker->text(200);
            if ( 'partial' === $paymentstatus ) {
              $data['payment_status'] = 'Success';
            }

            if ( $amount >=  $total_amount ) {
              $credit_note->credit_status = 'Closed';
              $credit_note->save();
            }

            \App\CreditNotePayment::create( $data );
            $this->insertHistory( array('id' => $id, 'comments' => 'invoices-payment-inserted', 'operation_type' => 'payment' ), 'CreditNote' );
          }
        }
        
       
              

       
        // Contracts.
        $number = rand(50, 100);
        for( $i = 0; $i < $number; $i++ ) {
          $customer = \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', CUSTOMERS_TYPE);
                          })->first();
          $after_days = rand(1, 99);
          $data = [
              'subject' => $faker->word,
              'address' => $faker->address,
              'invoice_prefix' => $faker->sentence( rand(3, 10) ),
              'show_quantity_as' => $faker->randomElement(['Qty', 'Quantity']),
              'contract_value' => $faker->randomFloat(2,1),
              'contract_type_id' => \Modules\Contracts\Entities\ContractType::inRandomOrder()->first()->id,
              'visible_to_customer' => $faker->randomElement(['yes', 'no']),
              'invoice_no' => $faker->numberBetween(1,10000 ),
              'status' => $faker->randomElement(['Published', 'Draft']),
              'reference' => $faker->word,
              'invoice_date' => $faker->date('Y-m-d'),
              'invoice_due_date' => $faker->date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$after_days.' days')),
              //'invoice_notes' =>$faker->sentence,
              
              'customer_id' => $customer->id,
              'currency_id' => $customer->currency_id,
              'tax_id' => \App\Tax::inRandomOrder()->first()->id,
              'discount_id' => \App\Discount::inRandomOrder()->first()->id,
              //'products' => $faker->name,
              'slug' => $faker->slug,
              'recurring_period_id' => \Modules\RecurringPeriods\Entities\RecurringPeriod::inRandomOrder()->first()->id,
              'amount' => $faker->randomFloat(2,1),
              'delivery_address' => $faker->address,
              'admin_notes' => $faker->text(200),
              'sale_agent' => \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', CONTACT_SALE_AGENT);
                          })->first()->id,
              'terms_conditions' => $faker->text(200),
              'paymentstatus' => $faker->randomElement(['Delivered', 'On-Hold', 'Accepted', 'Rejected']),
             
          ];
          $contract = \Modules\Contracts\Entities\Contract::create( $data );

          $id = $contract->id;

          $history = [
            'id' => $id, 
            'comments' => 'contract-created',
            'operation_type' => 'crud',
          ];
          $this->insertHistory($history, 'Contract');
        }
        
        
        // Client Projects.
        $number = rand(50, 100);
        for( $i = 0; $i < $number; $i++ ) {
          $client = \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', CONTACT_CLIENT_TYPE);
                          })->first();
          $after_days = rand(1, 180);
          $data = [
              'title' => $faker->word,
              'budget' => $faker->randomFloat(2,1),
              'phase' => $faker->randomElement(['I', 'II', 'III', 'IV', 'V']),
              'start_date' => $faker->date('Y-m-d'),
              'due_date' => $faker->date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$after_days.' days')),
              'description' => $faker->text(200),
              'priority' => $faker->randomElement(['Low', 'Medium', 'High', 'Urgent']),
              'status_id' => \App\ProjectStatus::inRandomOrder()->first()->id,
              'demo_url' => $faker->url,
              'client_id' => $client->id,
              'billing_type_id' => \App\ProjectBillingType::inRandomOrder()->first()->id,
              'progress_from_tasks' => $faker->randomElement(['yes', 'no',]),
              'project_rate_per_hour' => $faker->randomFloat(2,1),
              'estimated_hours' => $faker->randomFloat(2,1),
              'hourly_rate' => $faker->randomFloat(2,1),
              'currency_id' => $client->currency_id,             
          ];
          $project = \App\ClientProject::create( $data );

          $employees = \App\Contact::inRandomOrder()->whereHas("contact_type",
                          function ($query) {
                          $query->where('id', EMPLOYEES_TYPE);
                          })->get()->pluck('id')->toArray();
          $project->assigned_to()->sync( $employees );

          $tabs = \App\ProjectTab::inRandomOrder()->get()->pluck('id')->toArray();
          $project->project_tabs()->sync( $tabs );

          for ($t = 0; $t < rand(1, 10); $t++ ) {
            $startdate = date('Y-m-d H:i:s');
            $after_days = rand(1, 180);
            $task_data = [
              'name' => $faker->word,
              'description' => $faker->text(200),
              'priority' => \Modules\DynamicOptions\Entities\DynamicOption::where('module', 'projecttasks')->where('type', 'priorities')->inRandomOrder()->first()->id,
              'startdate' => $startdate,
              'duedate' => date('Y-m-d H:i:s', strtotime($startdate. ' + '.$after_days.' days')),
              'project_id' => $project->id,
            ];
            $task = \App\ProjectTask::create( $task_data );
          } 
        } // Projects end
        
    }

    private function randomProducts()
    {
      $faker = Faker::create();
      $qty = rand(1,10);
      $products_sync = [];
      $grand_total = 0;
      for( $i = 0; $i < $qty; $i++ ) {
        $product = \App\Product::inRandomOrder()->first();
        $tax_type = $discount_type = array_rand( [ 'percent', 'value' ], 1);
        
        $product_price = rand(1, $product->sale_price);
        $product_qty = rand(1, $product->stock_quantity );
        $product_amount = $product_price * $product_qty;

        $product_tax = $tax_value = rand(1,99);
        if ( $tax_type == 'percent' ) {
          $tax_value = ( $product_amount * $product_tax) / 100;
        }

        $product_discount = $discount_value = rand(1,99);
        if ( $discount_type == 'percent' ) {
          $discount_value = ( $product_amount * $discount_value) / 100;
        }

        $amount = ($product_qty * $product_price) + $tax_value - $discount_value;

        $grand_total += $amount;

        $sync_product = [
        'product_id' => $product->id,
        'product_name' => $product->name,
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
        'pid' => $product->id,
        'stock_quantity' => $product->stock_quantity,              
        'product_description' => $product->product_description,
        ];
        $products_sync[] = $sync_product;
      }
      return [ 'products' => $products_sync, 'grand_total' => $grand_total];
    }

    function insertHistory( $data, $type )
    {
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
          'browser' => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Fake Data',
          // 'purchase_order_id' => $id,
          'comments' => $comments,
          'operation_type' => $operation_type,
      );
      switch( $type ) {
        case 'PO':
          $log['purchase_order_id'] = $data['id'];
          \App\PurchaseOrderHistory::create( $log );
          break;
        case 'Invoice':
          $log['invoice_id'] = $data['id'];
          \App\InvoicesHistory::create( $log );
          break;
        case 'CreditNote':
          $log['credit_note_id'] = $data['id'];
          \App\CreditNoteHistory::create( $log );
          break;
        case 'Quote':
          $log['quote_id'] = $data['id'];
          \Modules\Quotes\Entities\QuoteHistory::create( $log );
          break;
        case 'Proposal':
          $log['proposal_id'] = $data['id'];
          \Modules\Proposals\Entities\ProposalHistory::create( $log );
          break;
        case 'Contract':
          $log['contract_id'] = $data['id'];
          \Modules\Contracts\Entities\ContractHistory::create( $log );
          break;
      }
    }
}
