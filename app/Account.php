<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Account
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $initial_balance
 * @property string $account_number
 * @property string $contact_person
 * @property string $phone
 * @property string $url
*/
class Account extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'initial_balance', 'account_number', 'contact_person', 'phone', 'url'];
    protected $hidden = [];
    public static $searchable = [
        'name',
    ];

    /**
    * Array of our custom model events declared under model property $observables
    * @var array
    */
  
    protected $observables = [
        'observeincrement',
        'observedecrement',
    ];
    
    public static function boot()
    {
        parent::boot();

        Account::observe(new \App\Observers\UserActionsObserver);

        Account::observe(new \App\Observers\AccountCrudActionObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
      * Publish increment & fire custom model event
      *
      */
      public function observeincrement()
      {
        
        // logic to update increment status goes here
        
        // fire custom event on the model
        $this->fireModelEvent('observeincrement', false);
        
      }

      /**
      * Publish decrement & fire custom model event
      *
      */
      public function observedecrement()
      {
        
        // logic to update decrement status goes here
        
        // fire custom event on the model
        $this->fireModelEvent('observedecrement', false);
        
      }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setNameAttribute($input)
    {
        $this->attributes['name'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setInitialBalanceAttribute($input)
    {
        $this->attributes['initial_balance'] = $input ? $input : 0;
    }

    public function getNameBalanceAttribute() {
        return $this->attributes['name'] . ' (' . digiCurrency( $this->attributes['initial_balance'] ) . ')';
    }
    
}
