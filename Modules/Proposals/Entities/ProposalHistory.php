<?php
namespace Modules\Proposals\Entities;

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
class ProposalHistory extends Model
{
    use SoftDeletes;

    protected $table = 'proposals_history';
    protected $fillable = ['ip_address', 'country', 'city', 'browser', 'proposal_id', 'comments'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ProposalHistory::observe(new \App\Observers\UserActionsObserver);
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
        return $this->belongsTo(Proposal::class, 'invoice_id');
    }
    
}
