<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class CreditNotePayment extends Model
{
    use SoftDeletes;

    protected $fillable = ['date', 'amount', 'transaction_id', 'credit_note_id', 'account_id', 'paymentmethod', 'description', 'payment_status', 'transaction_data', 'slug'];
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
        $this->attributes['credit_note_id'] = $input ? $input : null;
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
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
    
    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id')->withTrashed();
    }
    
}
