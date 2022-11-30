<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class InvoicesHistory
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $contact
*/
class PurchaseOrderHistory extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_orders_history';
    protected $fillable = ['ip_address', 'country', 'city', 'browser', 'purchase_order_id', 'comments'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        PurchaseOrderHistory::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setInvoiceIdAttribute($input)
    {
        $this->attributes['purchase_order_id'] = $input ? $input : null;
    }
    
    public function invoice()
    {
        return $this->belongsTo(PurchaseOrder::class, 'invoice_id');
    }
    
}
