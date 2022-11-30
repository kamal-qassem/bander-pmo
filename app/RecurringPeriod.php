<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RecurringPeriod
 *
 * @package App
 * @property string $title
 * @property string $value
 * @property text $description
*/
class RecurringPeriod extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'value', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        RecurringPeriod::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
