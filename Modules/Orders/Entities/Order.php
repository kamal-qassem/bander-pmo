<?php
namespace Modules\Orders\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Scopes\CustomerScope;

/**
 * Class Order
 *
 * @package App
 * @property string $customer
 * @property enum $status
 * @property decimal $price
 * @property string $billing_cycle
*/
class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['status', 'price', 'customer_id', 'currency_id', 'billing_cycle_id', 'products', 'slug', 'is_recurring', 'total_cycles', 'cycles', 'recurring_type', 'recurring_value', 'last_recurring_date', 'is_recurring_from', 'invoice_date', 'invoice_due_date', 'delivery_address'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Order::observe(new \App\Observers\UserActionsObserver);

        if ( ! defined('CRON_JOB') ) {
            if ( isCustomer() ) {
                static::addGlobalScope(new CustomerScope);
            }

            static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
        }
    }

    public static $enum_status = ["Pending" => "Pending", "Active" => "Active" ];

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
    public function setPriceAttribute($input)
    {
        $this->attributes['price'] = $input ? $input : null;
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
    public function setBillingCycleIdAttribute($input)
    {
        $this->attributes['billing_cycle_id'] = $input ? $input : null;
    }
    
    public function customer()
    {
        return $this->belongsTo(\App\Contact::class, 'customer_id')->withDefault();
    }
    
    public function billing_cycle()
    {
        return $this->belongsTo(\App\RecurringPeriod::class, 'billing_cycle_id')->withDefault()->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany(OrdersPayments::class, 'order_id')->withTrashed();
    }

    public function payment( $order_id )
    {
        return OrdersPayments::where('order_id', '=', $order_id)->first();
    }

    public function order_products()
    {
        return $this->belongsToMany(Order::class, 'order_products');
    }

      public function currency()
    {
        return $this->belongsTo(\App\Currency::class, 'currency_id')->withDefault()->withTrashed();
    }
    
    public function attached_products( $id )
    {
        return Order::select(['pop.*'])
            ->join('order_products as pop', 'pop.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'pop.product_id')
            ->where('orders.id', $id)->get();
    }

    public function getBillingCycleDisplayAttribute()
    {
        $is_recurring = ( $this->is_recurring ) ? $this->is_recurring : $this->attributes['is_recurring'];
        $total_cycles = ( $this->total_cycles ) ? $this->total_cycles : $this->attributes['total_cycles']; // Total cycles completed
        $cycles = ( $this->cycles ) ? $this->cycles : $this->attributes['cycles']; // Number of times repeat
        $recurring_type = ( $this->recurring_type ) ? $this->recurring_type : $this->attributes['recurring_type'];
        $recurring_value = ( $this->recurring_value ) ? $this->recurring_value : $this->attributes['recurring_value'];
        $str = trans('orders::global.orders.onetime');
        if ( 'yes' === $is_recurring && ! empty( $recurring_value ) ) {
            if ( empty( $cycles ) ) {
                $str = 'Recurring invoice for infinity cycles will repeat once in ' . $recurring_value . $recurring_type;
            } else {
                $str = 'Recurring invoice for '.$cycles.' cycles will repeat once in ' . $recurring_value . $recurring_type;
            }
        }
        return $str;
    }
}
