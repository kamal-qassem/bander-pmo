<?php

namespace Modules\Contracts\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;


class Contract extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['subject', 'contract_value','contract_type_id','address', 'invoice_prefix', 'show_quantity_as', 'invoice_no', 'status', 'reference', 'invoice_date', 'invoice_due_date', 'invoice_notes', 'amount', 'products', 'paymentstatus', 'customer_id', 'currency_id', 'tax_id', 'discount_id', 'recurring_period_id', 'slug', 'delivery_address', 'show_delivery_address', 'admin_notes','terms_conditions', 'prevent_overdue_reminders', 'created_by_id', 'invoice_number_format', 'invoice_number_separator', 'invoice_number_length','visible_to_customer'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Contract::observe(new \App\Observers\UserActionsObserver);

        if ( ! defined('CRON_JOB') ) {
            if ( isCustomer() ) {
                static::addGlobalScope(new \App\Scopes\ContractCustomerScope);
            }
        }
        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_status = ["Published" => "Published", "Draft" => "Draft"];

    public static $enum_paymentstatus = ["Unpaid" => "Unpaid", "Paid" => "Paid", "Partial" => "Partial", "Cancelled" => "Cancelled", "Due" => "Due"];
    public static $enum_visible_to_customer = ["yes" => "Yes", "no" => "No"];
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

    public function setContractTypeIdAttribute($input)
    {
        $this->attributes['contract_type_id'] = $input ? $input : null;
    }
    /**
     * Set to null if empty
     * @param $input
     */
    public function setCurrencyIdAttribute($input)
    {
        $this->attributes['currency_id'] = $input ? $input : null;
    }

    public function setContractIdAttribute($input)
    {
        $this->attributes['contract_id'] = $input ? $input : null;
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
    public function setRecurringPeriodIdAttribute($input)
    {
        $this->attributes['recurring_period_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
   
    public function customer()
    {
        return $this->belongsTo(\App\Contact::class, 'customer_id')->withDefault();
    }
    
    public function contract_type()
    {
        return $this->belongsTo(\App\ContractType::class, 'contract_type_id')->withDefault();
    }
	

    
    public function currency()
    {
        return $this->belongsTo(\App\Currency::class, 'currency_id')->withDefault()->withTrashed();
    }
    
   
    
    public function recurring_period()
    {
        return $this->belongsTo(\Modules\RecurringPeriods\Entities\RecurringPeriod::class, 'recurring_period_id')->withDefault()->withTrashed();
    }

    public function transactions()
    {
        return $this->hasMany(ContractPayment::class)->orderBy('id', 'DESC')->withTrashed();
    }

    public function history()
    {
        return $this->hasMany(ContractHistory::class)->orderBy('id', 'DESC')->withTrashed();
    }

    public function created_by()
    {
        return $this->belongsTo(\App\User::class, 'created_by_id')->withDefault();
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function getCurrencyAmountAttribute($input)
    {
        return digiCurrency( $this->attributes['amount'] );
    }


}
