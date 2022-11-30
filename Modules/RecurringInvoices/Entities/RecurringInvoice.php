<?php

namespace Modules\RecurringInvoices\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

use App\Scopes\InvoiceCustomerScope;
use App\Scopes\InvoiceSaleAgentScope;

class RecurringInvoice extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['title', 'address', 'invoice_prefix', 'show_quantity_as', 'invoice_no', 'status', 'reference', 'invoice_date', 'invoice_due_date', 'invoice_notes', 'amount', 'products', 'paymentstatus', 'customer_id', 'currency_id', 'tax_id', 'discount_id', 'recurring_period_id', 'slug', 'delivery_address', 'show_delivery_address', 'admin_notes', 'sale_agent', 'terms_conditions', 'prevent_overdue_reminders', 'is_recurring', 'total_cycles', 'cycles', 'recurring_value', 'recurring_type', 'last_recurring_date','created_by_id', 'quote_id', 'invoice_number_format', 'invoice_number_separator', 'invoice_number_length'];
    protected $hidden = [];
    public static $searchable = [
    ];
	
	protected $table = 'invoices';
    
    public static function boot()
    {
        parent::boot();

        RecurringInvoice::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope('is_recurring', function (Builder $builder) {
            $builder->where('invoices.is_recurring', '=', 'yes');
        });
        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
        
        if ( ! defined('CRON_JOB') ) {
            if ( isCustomer() ) {
                static::addGlobalScope(new InvoiceCustomerScope);
            }
            if ( isSalesPerson() ) {
                static::addGlobalScope(new InvoiceSaleAgentScope);
            }
        }
    }
	
	

    public static $enum_status = ["Published" => "Published", "Draft" => "Draft"];

    public static $enum_paymentstatus = ["Unpaid" => "Unpaid", "Paid" => "Paid", "Partial" => "Partial", "Cancelled" => "Cancelled", "Due" => "Due"];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCustomerIdAttribute($input)
    {
        $this->attributes['customer_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCurrencyIdAttribute($input)
    {
        $this->attributes['currency_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function getInvoiceNumberDisplayAttribute($input)
   {
        $invoice_number_format = ( $this->invoice_number_format ) ? $this->invoice_number_format : $this->attributes['invoice_number_format'];
        $invoice_number_separator = ( $this->invoice_number_separator ) ? $this->invoice_number_separator : $this->attributes['invoice_number_separator'];
        $invoice_number_length = ( $this->invoice_number_length ) ? $this->invoice_number_length : $this->attributes['invoice_number_length'];
        $invoice_no = ( $this->invoice_no ) ? $this->invoice_no : $this->attributes['invoice_no'];
        $invoice_prefix = ( $this->invoice_prefix ) ? $this->invoice_prefix : $this->attributes['invoice_prefix'];

        $invoice_date = ( $this->invoice_date ) ? $this->invoice_date : $this->attributes['invoice_date'];

        if ( empty( $invoice_date ) ) {
            $invoice_number_format = 'numberbased';
        }
        

       
        $invoice_no_display = $invoice_no;
        if ( ! empty( $invoice_number_length ) ) {
            $invoice_no = str_pad($invoice_no, $invoice_number_length, 0, STR_PAD_LEFT);
        }
        if ( 'yearbased' === $invoice_number_format ) {
            $invoice_no_display = date('Y', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
        } elseif ( 'year2digits' === $invoice_number_format ) {
            $invoice_no_display = date('y', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
        } elseif ( 'yearmonthnumber' === $invoice_number_format ) {
            $invoice_no_display = date('Y', strtotime( $invoice_date ) ) . $invoice_number_separator . date('m', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
        } elseif ( 'yearbasedright' === $invoice_number_format ) {
            $invoice_no_display = $invoice_no . $invoice_number_separator . date('Y', strtotime( $invoice_date ) );
        } elseif ( 'year2digitsright' === $invoice_number_format ) {
            $invoice_no_display = $invoice_no . $invoice_number_separator . date('y', strtotime( $invoice_date ) );
        } elseif ( 'numbermonthyear' === $invoice_number_format ) {
            $invoice_no_display = $invoice_no . $invoice_number_separator . date('m', strtotime( $invoice_date ) ) . $invoice_number_separator . date('Y', strtotime( $invoice_date ) );
        }
        return $invoice_prefix . $invoice_no_display;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setTaxIdAttribute($input)
    {
        $this->attributes['tax_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setDiscountIdAttribute($input)
    {
        $this->attributes['discount_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setRecurringPeriodIdAttribute($input)
    {
        $this->attributes['recurring_period_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAmountAttribute($input)
    {
        $this->attributes['amount'] = $input ? $input : null;
    }
    
    public function customer()
    {
        return $this->belongsTo(\App\Contact::class, 'customer_id');
    }
	
	public function saleagent()
    {
        return $this->belongsTo(\App\Contact::class, 'sale_agent', 'id')->withDefault();
    }
    
    public function currency()
    {
        return $this->belongsTo(\App\Currency::class, 'currency_id')->withDefault()->withTrashed();
    }
    
    public function tax()
    {
        return $this->belongsTo(\App\Tax::class, 'tax_id')->withDefault()->withTrashed();
    }
    
    public function discount()
    {
        return $this->belongsTo(\App\Discount::class, 'discount_id')->withDefault()->withTrashed();
    }
    
    public function recurring_period()
    {
        return $this->belongsTo(\Modules\RecurringPeriods\Entities\RecurringPeriod::class, 'recurring_period_id')->withDefault()->withTrashed();
    }

    public function transactions()
    {
        return $this->hasMany(RecurringInvoicePayment::class, 'invoice_id')->orderBy('id', 'DESC')->withTrashed();
    }

    public function history()
    {
        return $this->hasMany(RecurringInvoiceHistory::class, 'invoice_id')->orderBy('id', 'DESC')->withTrashed();
    }

    public function created_by()
    {
        return $this->belongsTo(\App\User::class, 'created_by_id')->withDefault();
    }
	
	 public function allowed_paymodes()
    {
        return $this->belongsToMany(\App\PaymentGateway::class, 'invoice_paymentmodes', 'invoice_id')->withTrashed();
    }

    public function invoice_products()
    {
        return $this->belongsToMany(RecurringInvoice::class, 'invoice_products', 'invoice_id', 'product_id');
    }

    public function attached_products( $id )
    {
        return RecurringInvoice::select(['pop.*'])
            ->join('invoice_products as pop', 'pop.invoice_id', '=', 'invoices.id')
            ->join('products', 'products.id', '=', 'pop.product_id')
            ->where('invoices.id', $id)->get();
    }

    public function attached_tasks( $id, $select = false )
    {
        if ( $select ) {
            return \App\Invoice::select([
                    'project_tasks.id',
                    'project_tasks.name',
                    'project_tasks.description',
                    'project_tasks.startdate',
                    'project_tasks.duedate',
                    'project_tasks.datefinished',
                    'project_tasks.billable',
                    'project_tasks.billed',
                    'project_tasks.hourly_rate',
                ])
                ->join('invoice_products_tasks as pop', 'pop.invoice_id', '=', 'invoices.id')
                ->join('project_tasks', 'project_tasks.id', '=', 'pop.task_id')
                ->where('invoices.id', $id)->get();
        } else {
            return \App\Invoice::select(['pop.*'])
                ->join('invoice_products_tasks as pop', 'pop.invoice_id', '=', 'invoices.id')
                ->join('project_tasks', 'project_tasks.id', '=', 'pop.task_id')
                ->where('invoices.id', $id)->get();
        }
    }

    public function attached_expenses( $id, $select = false )
    {
        if ( $select ) {
            return \App\Invoice::select([
                    'expenses.id',
                    'expenses.name',
                    'expenses.entry_date',
                    'expenses.amount',
                    'expenses.description',
                    'expenses.ref_no',
                    'expenses.project_id',
                    'expenses.billable',
                    'expenses.billed',
                ])
                ->join('invoice_products_expenses as pop', 'pop.invoice_id', '=', 'invoices.id')
                ->join('expenses', 'expenses.id', '=', 'pop.expense_id')
                ->where('invoices.id', $id)->get();
        } else {
            return \App\Invoice::select(['pop.*'])
                ->join('invoice_products_expenses as pop', 'pop.invoice_id', '=', 'invoices.id')
                ->join('expenses', 'expenses.id', '=', 'pop.expense_id')
                ->where('invoices.id', $id)->get();
            }
    }

    public function update_invoice_status( $id )
    {
        $total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', '=', $id)->where('payment_status', 'Success')->sum('amount');
        $total_used = \App\CreditNoteCredit::where('invoice_id', '=', $id)->sum('amount');
        $invoice = Invoice::find( $id );
        if ( $invoice ) {
            $total_paid += $total_used;
            
            if ( $total_paid >= $invoice->amount ) {
                $invoice->paymentstatus = 'paid';
            } else if( $total_paid == 0 ) {
                $invoice->paymentstatus = 'unpaid';
            } else if( $total_paid < $invoice->amount ) {
                $invoice->paymentstatus = 'partial';
            }
            $invoice->save();
        }
    }
}
