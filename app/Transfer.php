<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transfer
 *
 * @package App
 * @property string $from
 * @property string $to
 * @property string $date
 * @property double $amount
 * @property string $ref_no
 * @property string $payment_method
 * @property text $description
*/
class Transfer extends Model
{
    use SoftDeletes;

    protected $fillable = ['date', 'amount', 'ref_no', 'description', 'from_id', 'to_id', 'payment_method_id'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Transfer::observe(new \App\Observers\UserActionsObserver);

       

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setFromIdAttribute($input)
    {
        $this->attributes['from_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setToIdAttribute($input)
    {
        $this->attributes['to_id'] = $input ? $input : null;
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
     * Set attribute to date format
     * @param $input
     */
    public function setAmountAttribute($input)
    {
        if ($input != '') {
            $this->attributes['amount'] = $input;
        } else {
            $this->attributes['amount'] = null;
        }
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setPaymentMethodIdAttribute($input)
    {
        $this->attributes['payment_method_id'] = $input ? $input : null;
    }
    
    public function from()
    {
        return $this->belongsTo(Account::class, 'from_id')->withDefault()->withTrashed();
    }
    
    public function to()
    {
        return $this->belongsTo(Account::class, 'to_id')->withDefault()->withTrashed();
    }
    
    public function payment_method()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_method_id')->withDefault()->withTrashed();
    }
    
}
