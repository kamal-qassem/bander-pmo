<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class Expense
 *
 * @package App
 * @property string $account
 * @property string $expense_category
 * @property string $entry_date
 * @property string $amount
 * @property text $description
 * @property string $payee
 * @property string $payment_method
 * @property string $ref_no
*/
class Expense extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = ['entry_date', 'amount', 'description', 'ref_no', 'account_id', 'expense_category_id', 
    'payee_id', 'payment_method_id', 'slug','name', 'is_recurring', 'recurring_period_id','recurring_value', 'is_recurring_from', 'project_id', 'create_invoice_billable', 'send_invoice_to_customer', 'billable','billed', 'invoice_id', 'tax_id', 'tax_value', 'tax_type', 'currency_id','recurring_type','cycles','total_cycles'
    ];
    protected $hidden = [];
    public static $searchable = [ 'description', 'ref_no' ];
    
    public static function boot()
    {
        parent::boot();

        Expense::observe(new \App\Observers\UserActionsObserver);

        if ( ! app()->runningInConsole() ) {
            Expense::observe(new \App\Observers\ExpenseCrudActionObserver);

            Expense::observe(new \App\Observers\AccountCrudActionObserver);
        }

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
    public function setExpenseCategoryIdAttribute($input)
    {
        $this->attributes['expense_category_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
  

    /**
     * Set to null if empty
     * @param $input
     */
    public function setPayeeIdAttribute($input)
    {
        $this->attributes['payee_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setPaymentMethodIdAttribute($input)
    {
        $this->attributes['payment_method_id'] = $input ? $input : null;
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withTrashed();
    }
    
    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id')->withDefault();
    }
    
    public function payee()
    {
        return $this->belongsTo(Contact::class, 'payee_id')->withDefault();
    }
    
    public function payment_method()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_method_id')->withTrashed();
    }
    
    public function getCurrencyAmountAttribute()
    {
        return digiCurrency( $this->attributes['amount'] );
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withDefault()->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault()->withTrashed();
    }

      public function recurring_period()
    {
        return $this->belongsTo(RecurringPeriod::class, 'recurring_period_id')->withDefault()->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Invoice::class, 'invoice_id')->withDefault()->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(\App\ClientProject::class, 'project_id')->withDefault()->withTrashed();
    }
}
