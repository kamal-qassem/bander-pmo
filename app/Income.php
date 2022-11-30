<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class Income
 *
 * @package App
 * @property string $account
 * @property string $income_category
 * @property string $entry_date
 * @property string $amount
 * @property text $description
 * @property string $payer
 * @property string $pay_method
 * @property string $ref_no
*/
class Income extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = ['entry_date', 'amount', 'original_amount', 'original_currency_id', 'description', 'ref_no', 'account_id', 'income_category_id', 'payer_id','payer_name', 'pay_method_id', 'slug'];
    protected $hidden = [];
    public static $searchable = [ 'description', 'ref_no'
    ];

    public static function boot()
    {
        parent::boot();

        Income::observe(new \App\Observers\UserActionsObserver);

        Income::observe(new \App\Observers\AccountCrudActionObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
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
     * Set to null if empty
     * @param $input
     */
    public function setIncomeCategoryIdAttribute($input)
    {
        $this->attributes['income_category_id'] = $input ? $input : null;
    }

    
	

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getEntryDateAttribute($input)
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
    public function setPayerIdAttribute($input)
    {
        $this->attributes['payer_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setPayMethodIdAttribute($input)
    {
        $this->attributes['pay_method_id'] = $input ? $input : null;
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withTrashed();
    }
    
    public function income_category()
    {
        return $this->belongsTo(\App\IncomeCategory::class, 'income_category_id')->withDefault();
    }
    
    public function payer()
    {
        return $this->belongsTo(Contact::class, 'payer_id')->withDefault();
    }
    
    public function pay_method()
    {
        return $this->belongsTo(PaymentGateway::class, 'pay_method_id')->withDefault()->withTrashed();
    }

    public function getCurrencyAmountAttribute()
    {
        return digiCurrency( $this->attributes['amount'] );
    }
    
}
