<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class IncomeCategory
 *
 * @package App
 * @property string $name
*/
class IncomeCategory extends Model
{
    protected $fillable = ['name'];
    protected $hidden = [];
    
    
    public static function boot()
    {
        parent::boot();

        IncomeCategory::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
