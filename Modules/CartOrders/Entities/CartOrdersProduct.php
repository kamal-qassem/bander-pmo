<?php
namespace Modules\CartOrders\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @package App
 * @property string $cart_order_id
 * @property enum $quantity
 * @property decimal $product_id
*/
class CartOrdersProduct extends Model
{

    protected $fillable = ['quantity', 'cart_order_id', 'product_id', 'slug'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        CartOrder::observe(new \Modules\CartOrders\Observers\UserActionsObserver);
    }

    public static $enum_status = ["Pending" => "Pending", "Active" => "Active"];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCartOrderIdAttribute($input)
    {
        $this->attributes['cart_order_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setProductIdAttribute($input)
    {
        $this->attributes['product_id'] = $input ? $input : null;
    }
    
    public function order()
    {
        return $this->belongsTo(\Modules\CartOrders\Entities\CartOrder::class, 'cart_order_id');
    }
    
    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }
    
}
