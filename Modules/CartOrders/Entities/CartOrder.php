<?php
namespace Modules\CartOrders\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @package App
 * @property string $customer
 * @property enum $status
 * @property decimal $price
 * @property string $billing_cycle
*/
class CartOrder extends Model
{
    
    protected $fillable = ['status', 'price', 'customer_id', 'billing_cycle_id', 'products', 'slug'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        CartOrder::observe(new \App\Observers\UserActionsObserver);
    }

    public static $enum_status = ["Pending" => "Pending", "Active" => "Active"];

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
    public function setBillingCycleIdAttribute($input)
    {
        $this->attributes['billing_cycle_id'] = $input ? $input : null;
    }
    
    public function customer()
    {
        return $this->belongsTo(\App\Contact::class, 'customer_id');
    }
    
    public function billing_cycle()
    {
        return $this->belongsTo(\App\RecurringPeriod::class, 'billing_cycle_id')->withTrashed();
    }
    
}
