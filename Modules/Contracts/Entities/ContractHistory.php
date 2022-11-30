<?php
namespace Modules\Contracts\Entities;

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
class ContractHistory extends Model
{
    use SoftDeletes;

    protected $table = 'contracts_history';
    protected $fillable = ['ip_address', 'country', 'city', 'browser', 'contract_id', 'comments'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ContractHistory::observe(new \App\Observers\UserActionsObserver);
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
        return $this->belongsTo(Contract::class, 'invoice_id');
    }
    
}
