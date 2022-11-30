<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Currency
 *
 * @package App
 * @property string $name
 * @property string $symbol
 * @property string $code
 * @property string $rate
 * @property enum $status
*/
class Currency extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'symbol', 'code', 'rate', 'status', 'is_default'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Currency::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope('active_currency', function (Builder $builder) {
              $builder->where('currencies.status', 'Active');
          });

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];
    public static $enum_is_default = ["yes" => "Yes", "no" => "No"];
    
}
