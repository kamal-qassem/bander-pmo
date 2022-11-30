<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductsReturn
 *
 * @package App
 * @property string $subject
 * @property string $customer
 * @property string $currency
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
 * @property string $tax
 * @property string $discount
 * @property string $ware_house
*/
class ProductsReturn extends Model
{
    use SoftDeletes;

    protected $fillable = ['subject', 'status', 'address', 'invoice_prefix', 'show_quantity_as', 'invoice_no', 'reference', 'order_date', 'order_due_date', 'update_stock', 'notes', 'customer_id', 'currency_id', 'tax_id', 'discount_id', 'ware_house_id', 'products', 'amount', 'delivery_address', 'show_delivery_address', 'admin_notes', 'sale_agent', 'terms_conditions', 'prevent_overdue_reminders'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ProductsReturn::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_status = ["Published" => "Published", "Draft" => "Draft"];
    public static $enum_update_stock = ["Yes" => "Yes", "No" => "No"];

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
     * Set attribute to date format
     * @param $input
     */
    public function setOrderDateAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['order_date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['order_date'] = null;
        }
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getOrderDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setOrderDueDateAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['order_due_date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['order_due_date'] = null;
        }
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getOrderDueDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
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
    public function setWareHouseIdAttribute($input)
    {
        $this->attributes['ware_house_id'] = $input ? $input : null;
    }
    
    public function customer()
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }
    
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withTrashed();
    }
    
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withTrashed();
    }
    
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id')->withTrashed();
    }
    
    public function ware_house()
    {
        return $this->belongsTo(Warehouse::class, 'ware_house_id')->withTrashed();
    }
    
}
