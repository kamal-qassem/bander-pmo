<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

use App\Scopes\PoSupplierScope;

/**
 * Class PurchaseOrder
 *
 * @package App
 * @property string $customer
 * @property string $subject
 * @property enum $status
 * @property text $address
 * @property string $invoice_prefix
 * @property string $show_quantity_as
 * @property string $invoice_no
 * @property string $reference
 * @property string $order_date
 * @property string $order_due_date
 * @property string $update_stock
 * @property text $notes
 * @property string $currency
 * @property string $warehouse
 * @property string $tax
 * @property string $discount
 * @property decimal $amount
*/
class PurchaseOrder extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['subject', 'status', 'address', 'invoice_prefix', 'show_quantity_as', 'invoice_no', 'reference', 'order_date', 'order_due_date', 'update_stock', 'notes', 'amount', 'customer_id', 'currency_id', 'warehouse_id', 'tax_id', 'discount_id', 'slug', 'products', 'paymentstatus', 'recurring_period_id', 'delivery_address', 'show_delivery_address', 'admin_notes', 'sale_agent', 'terms_conditions', 'prevent_overdue_reminders', 'invoice_number_format', 'invoice_number_separator', 'invoice_number_length'];
    protected $hidden = [];
    public static $searchable = [ 'subject', 'invoice_no'
    ];
    
    public static function boot()
    {
        parent::boot();

        PurchaseOrder::observe(new \App\Observers\UserActionsObserver);

        if ( ! defined('CRON_JOB') ) {
            if ( isSupplier() ) {
                static::addGlobalScope(new PoSupplierScope);
            }

            static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
        }
    }

    public static $enum_status = ["Published" => "Published", "Draft" => "Draft"];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCustomerIdAttribute($input)
    {
        $this->attributes['customer_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function getInvoiceNumberDisplayAttribute($input)
     {
        // dd( $this->attributes );
        $invoice_number_format = ( $this->invoice_number_format ) ? $this->invoice_number_format : $this->attributes['invoice_number_format'];
        $invoice_number_separator = ( $this->invoice_number_separator ) ? $this->invoice_number_separator : $this->attributes['invoice_number_separator'];
        $invoice_number_length = ( $this->invoice_number_length ) ? $this->invoice_number_length : $this->attributes['invoice_number_length'];
        $invoice_no = ( $this->invoice_no ) ? $this->invoice_no : $this->attributes['invoice_no'];
        $invoice_prefix = ( $this->invoice_prefix ) ? $this->invoice_prefix : $this->attributes['invoice_prefix'];
        $invoice_date = ( $this->order_date ) ? $this->order_date : $this->attributes['order_date'];
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
    public function setCurrencyIdAttribute($input)
    {
        $this->attributes['currency_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setWarehouseIdAttribute($input)
    {
        $this->attributes['warehouse_id'] = $input ? $input : null;
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
     * Set attribute to money format
     * @param $input
     */
    public function setAmountAttribute($input)
    {
        $this->attributes['amount'] = $input ? $input : null;
    }
	
	/**
     * Set attribute to money format
     * @param $input
     */
    public function getCurrencyAmountAttribute($input)
    {
        return digiCurrency( $this->attributes['amount'] );
    }
    
    public function customer()
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }
    
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withTrashed();
    }
    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->withTrashed();
    }
    
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withTrashed();
    }
    
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id')->withTrashed();
    }

    public function transactions()
    {
        return $this->hasMany(PurchaseOrderPayment::class)->orderBy('id', 'DESC')->withTrashed();
    }

    public function history()
    {
        return $this->hasMany(PurchaseOrderHistory::class)->orderBy('id', 'DESC')->withTrashed();
    }
	
	public function recurring_period()
    {
        return $this->belongsTo(\Modules\RecurringPeriods\Entities\RecurringPeriod::class, 'recurring_period_id')->withDefault()->withTrashed();
    }

    public function purchase_order_products()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_order_products');
    }

    public function attached_products( $id )
    {
        return PurchaseOrder::select(['pop.*'])
            ->join('purchase_order_products as pop', 'pop.purchase_order_id', '=', 'purchase_orders.id')
            ->join('products', 'products.id', '=', 'pop.product_id')
            ->where('purchase_orders.id', $id)->get();
    }
}
