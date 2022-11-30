<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class InvoicePayment
 *
 * @package App
 * @property string $invoice
 * @property string $date
 * @property string $account
 * @property decimal $amount
 * @property string $transaction_id
*/
class CreditNoteCredit extends Model
{

    protected $table = 'credit_note_credits';
    protected $fillable = ['invoice_id', 'credit_note_id', 'user_id', 'amount'];
    protected $hidden = [];
    public static $searchable = [
    ];

    public static function boot()
    {
        parent::boot();

        CreditNotePayment::observe(new \App\Observers\UserActionsObserver);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setInvoiceIdAttribute($input)
    {
        $this->attributes['invoice_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreditNoteIdAttribute($input)
    {
        $this->attributes['credit_note_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAmountAttribute($input)
    {
        $this->attributes['amount'] = $input ? $input : null;
    }
    
    public function invoice()
    {
        return $this->belongsTo(\App\CreditNote::class, 'credit_note_id')->withTrashed();
    }

    public function applied_invoice()
    {
        return $this->belongsTo(\App\Invoice::class, 'invoice_id')->withTrashed();
    }
    
    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id')->withTrashed();
    }
    
}
