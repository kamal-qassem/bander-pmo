<?php
namespace Modules\Orders\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Order
 *
 * @package App
 * @property string $customer
 * @property enum $status
 * @property decimal $price
 * @property string $billing_cycle
*/
class OrdersPayments extends Model
{
    use SoftDeletes;

    protected $fillable = ['date', 'amount', 'transaction_id', 'account_id', 'order_id', 'paymentmethod', 'description', 'slug', 'payment_status', 'transaction_data'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        OrdersPayments::observe(new \App\Observers\UserActionsObserver);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setAccountIdAttribute($input)
    {
        $this->attributes['account_id'] = $input ? $input : null;
    }

    
    /**
     * Set to null if empty
     * @param $input
     */
    public function setOrderIdAttribute($input)
    {
        $this->attributes['order_id'] = $input ? $input : null;
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id')->withTrashed();
    }
    
}
