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
class CreditNoteHistory extends Model
{
    use SoftDeletes;

    protected $table = 'credit_notes_history';
    protected $fillable = ['ip_address', 'country', 'city', 'browser', 'credit_note_id', 'comments', 'operation_type'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        CreditNoteHistory::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreditNoteIdAttribute($input)
    {
        $this->attributes['credit_note_id'] = $input ? $input : null;
    }
    
    public function credit_note()
    {
        return $this->belongsTo(CreditNote::class, 'credit_note_id');
    }
    
}
