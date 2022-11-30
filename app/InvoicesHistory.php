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
class InvoicesHistory extends Model
{
    use SoftDeletes;

    protected $table = 'invoices_history';
    protected $fillable = ['ip_address', 'country', 'city', 'browser', 'invoice_id', 'comments', 'operation_type'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        InvoicesHistory::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setInvoiceIdAttribute($input)
    {
        $this->attributes['invoice_id'] = $input ? $input : null;
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    
}
