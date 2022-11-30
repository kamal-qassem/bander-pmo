<?php
namespace Modules\Contracts\Entities;

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
class ContractPayment extends Model
{
    use SoftDeletes;

    protected $table = 'contracts_payments';

    protected $fillable = ['date', 'amount', 'transaction_id', 'contract_id', 'account_id', 'paymentmethod', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ContractPayment::observe(new \App\Observers\UserActionsObserver);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setInvoiceIdAttribute($input)
    {
        $this->attributes['recurring_invoice_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setDateAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['date'] = null;
        }
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
        return $this->belongsTo(Contract::class, 'invoice_id')->withTrashed();
    }
    
    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id')->withTrashed();
    }
    
}
